<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/14
 * Time: 16:18
 */

namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Jobs\AlipayJob;
use App\Models\Accounts;
use App\Models\BaseModel;
use App\Models\BUser;
use App\Models\Commission;
use App\Models\CommissionConfig;
use App\Models\ConfigBrand;
use App\Models\CreditLog;
use App\Models\Orders;
use App\Models\OrdersBill;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\OrdersPay;
use App\Models\OrdersRefund;
use App\Models\OrdersSn;
use App\Models\Setting;
use App\Models\WorkTask;
use App\Services\PayService;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SettlementController extends ApiBaseController
{
    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $orderStatus = $this->getParameter('order_status', '');

        $conditions = [];
        if (is_numeric($orderStatus)) {
            $conditions['pay_status'] = substr($orderStatus, 1);
        }
        $conditions['order_status'] = [5, 6];

        $vehicleNumber = $this->getParameter('vehicle_number', '');
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        if ($vehicleNumber) {
            $conditions['%vehicle_number%'] = $vehicleNumber;
        }
        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
        }
        $orderSn = $this->getParameter('order_sn', '');
        if ($orderSn) {
            $conditions['%order_sn%'] = $orderSn;
        }

        $list = [];
        $total = Orders::getInstance()->countListByConditions($conditions);
        if ($total) {

            $res = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);

            foreach ($res as $v) {
                $list[] = [
                    'order_id' => $v->id,
                    'order_sn' => $v->order_sn,
                    'vehicle_number' => $v->vehicle_number,
                    'plan_endtime' => $v->plan_endtime,
                    'order_status' => BusinessConstants::ORDER_PAY_STATUS[$v->pay_status],
                    'vehicle_owner' => $v->vehicle_owner,
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'end_time' => OrdersItems::getInstance()->getQualityEndTime($v->id),
                    'pay_status' => $v->pay_status,
                    'bill_operator' => Accounts::getInstance()->getUserNameByUid($v->bill_operator),
                    'total_cost' => $v->total_cost,
                    'arrive_time' => $v->arrive_time,
                    'contact_name' => $v->contact_name,
                    'phone' => $v->phone,
                ];
            }
        }

        $counts = Orders::getInstance()->groupCountByPay($beginTime, $endTime);

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => $counts
        ]);
    }

    public function savePayAction()
    {
        $orderId = $this->getIntegerParameter('order_id');
        $pays = $this->getParameter('pays', []);
        $this->mutexLock($orderId);

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }

        $sum = 0;
        foreach ($pays as $v) {
            $sum += $v['cost'];
        }

        if (floatval($sum) !== floatval($ret->total_cost)) {
            static::errorThrow('填写的付款总金额与需要支付的总金额不相符!');
        }

        $payOrderSn = OrdersSn::getInstance()->getPayOrderSn($orderId, $ret->order_sn);

        BaseModel::getInstance()->beginTransaction();

        $payStatus = 2;
        foreach ($pays as $v) {
            if ($v['way'] == BusinessConstants::PAY_STATUS_HOLD) {
                $payStatus = 1;
            }
            OrdersPay::getInstance()->insertByFields([
                'order_id' => $orderId,
                'pay_way' => $v['way'],
                'pay_cost' => $v['cost'],
                'dateline' => time()
            ]);
        }

        Orders::getInstance()->updateByFields(
            [
                'order_status' => 6,
                'pay_way' => 0,
                'pay_status' => 2,
                'bill_operator' => $this->uid,
                'pay_order_sn' => $payOrderSn,
                'pay_time' => time()
            ],
            ['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]
        );

        BaseModel::getInstance()->commitTransaction();
        parent::renderSuccessJson([], '支付成功');

    }

    public function aliPayAction()
    {
        $authCode = $this->getParameter('auth_code');
        $orderId = $this->getParameter('order_id');

        $this->mutexLock($orderId);

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }

        $payOrderSn = OrdersSn::getInstance()->getPayOrderSn($orderId, $ret->order_sn);
        $payResult = null;
        try {
            $payResult = PayService::getInstance()->alipay()->pos([
                'out_trade_no' => $payOrderSn,
                'total_amount' => $ret->total_cost,
                'subject' => '汽车服务消费订单-' . $ret->vehicle_number,
                'auth_code' => $authCode
            ]);
        } catch (\Exception $e) {
            //大额资金支付需要走一步队列查询最终支付状态
            if ($e->getCode() == 10003) {
                if (!PayService::getInstance()->setPayLock($orderId)) {
                    static::errorThrow('订单等待支付中，请等待60秒后!');
                }

                AlipayJob::dispatch($orderId, $payOrderSn, $this->uid)->onQueue(AlipayJob::QUEUE_NAME);

                parent::renderSuccessJson(
                    [
                        'status' => 'wait',
                        'order_id' => $orderId,
                        'order_sn' => $payOrderSn
                    ],
                    '等待客户支付确认'
                );

                return;
            } else {
                OrdersSn::getInstance()->updateByFields(['error_msg' => $e->getMessage()], ['pay_order_sn' => $payOrderSn]);
            }
        }

        if ($payResult && $payResult->code == 10000 && $payResult->msg == 'Success') {
            BaseModel::getInstance()->beginTransaction();

            if (!OrdersBill::getInstance()->setBillDone($this->uid, $payOrderSn, $orderId, BusinessConstants::PAY_STATUS_ALIPAY, $payResult)) {
                static::errorThrow('支付数据记录异常，请联系管理员排查!');
            }

            BaseModel::getInstance()->commitTransaction();

            CreditLog::getInstance()->addCreditByPhone($this->uid, $ret->order_sn, $ret->phone, floor($ret->total_cost), '消费奖励积分');

            parent::renderSuccessJson([
                'status' => 'succ',
                'order_id' => $orderId,
                'order_sn' => $payOrderSn
            ], '支付成功');
        }

        static::errorThrow('支付失败,请重试!');
    }

    public function wxPayAction()
    {

    }

    public function cashPayAction()
    {
        $orderId = $this->getParameter('order_id');
        $cash = $this->getFloatParameter('cash', 0);
        $this->mutexLock($orderId);

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }

        if (floatval($ret->total_cost) !== $cash) {
            static::errorThrow('输入的支付金额与订单金额不符，请重新输入！');
        }

        if (!OrdersBill::getInstance()->setBillDone($this->uid, $ret->order_sn, $ret->id, BusinessConstants::PAY_STATUS_CASH)) {
            static::errorThrow('支付操作失败，请确认订单状态!');
        }

        CreditLog::getInstance()->addCreditByPhone($this->uid, $ret->order_sn, $ret->phone, floor($ret->total_cost), '消费奖励积分');

        parent::renderSuccessJson();
    }

    public function cardPayAction()
    {
        $orderId = $this->getParameter('order_id');
        $cash = $this->getFloatParameter('cash', 0);
        $this->mutexLock($orderId);

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }
        if (floatval($ret->total_cost) !== $cash) {
            static::errorThrow('输入的支付金额与订单金额不符，请重新输入！');
        }

        if (!OrdersBill::getInstance()->setBillDone($this->uid, $ret->order_sn, $ret->id, BusinessConstants::PAY_STATUS_CARD)) {
            static::errorThrow('支付操作失败，请确认订单状态!');
        }

        CreditLog::getInstance()->addCreditByPhone($this->uid, $ret->order_sn, $ret->phone, floor($ret->total_cost), '消费奖励积分');

        parent::renderSuccessJson();
    }


    public function vipPayAction()
    {
        $orderId = $this->getParameter('order_id');
        $phone = $this->getParameter('phone');
        $this->mutexLock($orderId);

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }

        BaseModel::getInstance()->beginTransaction();

        $user = BUser::getInstance()->getOneByField('phone', $phone);

        if (!$user) {
            static::errorThrow('会员卡号验证失败，请确认会员卡正确!');
        }

        if (!BUser::getInstance()->chargeWallet($this->uid, $user->id, $ret->order_sn, $ret->total_cost, '会员卡余额消费') ||
            !OrdersBill::getInstance()->setBillDone($this->uid, $ret->order_sn, $ret->id, BusinessConstants::PAY_STATUS_VIPCARD, null, $phone)) {
            static::errorThrow('扣款账户余额失败,请确认账户余额充足。');
        }

        BaseModel::getInstance()->commitTransaction();

        CreditLog::getInstance()->addCreditByPhone($this->uid, $ret->order_sn, $ret->phone, floor($ret->total_cost), '消费奖励积分');

        parent::renderSuccessJson();
    }

    public function holdBillAction()
    {
        $orderId = $this->getParameter('order_id');
        $this->mutexLock($orderId);
        $conds = ['id' => $orderId, 'order_status' => 5, 'pay_status' => 0];
        $ret = Orders::getInstance()->getOneByFields($conds);
        if (!$ret) {
            static::errorThrow('当前订单无法进行支付操作，请检查订单状态!');
        }

        if (!Orders::getInstance()->updateByFields(['pay_status' => 1, 'bill_operator' => $this->uid, 'pay_time' => time(), 'pay_order_sn' => ''], $conds)) {
            static::errorThrow('挂账操作失败');
        }

        parent::renderSuccessJson();
    }

    public function viewOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status>' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单数据不存在');
        }

        $itemList = OrdersItems::getInstance()->getListByConditions(['order_id' => $orderId, 'enable' => 1], 0, BusinessConstants::MAX_RETURN_SIZE);
        $partList = OrdersParts::getInstance()->getListByConditions(
            [
                'order_id' => $orderId,
                'parts_status' => 1
            ], 0, BusinessConstants::MAX_RETURN_SIZE
        );

        // 获取施工人员信息
        $workTaskNames = WorkTask::getInstance()->getOperatorNameByOrderId($orderId);
        $commissions = Commission::getInstance()->getCommissionRateByOrderId($orderId);
        $operators = [];
        foreach ($itemList as $v) {
            $o = explode(',', $v->operators);
            foreach ($o as $uid) {
                $c = isset($commissions[$v->id][$uid]) ? ($commissions[$v->id][$uid] * 100) : 0;
                $operators[] = [
                    'username' => Accounts::getInstance()->getUserNameByUid($uid),
                    'op_type' => isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '',
                    'team' => (BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$v->op_type] == 'people') ? '' : $workTaskNames[$v->op_type],
                    'times' => $v->item_times,
                    'item_name' => $v->item_name,
                    'commission' => $c,
                    'commission_times' => $c * $v->item_times / 100
                ];
            }
        }

        foreach ($itemList as &$v) {
            $v->op_type = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            $v->total_price = $v->item_times * $v->item_cost;
        }
        unset($v);
        foreach ($partList as &$v) {
            $v->op_type = ($v->op_type && BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            $v->total_price = $v->sell_price * $v->amount;
        }
        unset($v);

        //获取支付信息数据
        $payList = [];
        $res = OrdersPay::getInstance()->getListByConditions([
            'order_id' => $orderId
        ], 0, BusinessConstants::MAX_RETURN_SIZE, 'id', 'ASC');

        if (count($res)) {
            foreach ($res as $v) {
                $payList[] = [
                    'way' => BusinessConstants::PAY_STATUS_SET[$v->pay_way],
                    'cost' => $v->pay_cost
                ];
            }
        } elseif ($orderInfo->pay_way) {
            OrdersPay::getInstance()->deleteByFields(['order_id' => $orderId]);
            $payList[] = [
                'way' => BusinessConstants::PAY_STATUS_SET[$orderInfo->pay_way],
                'cost' => $orderInfo->total_cost
            ];
            OrdersPay::getInstance()->insertByFields([
                'order_id' => $orderId,
                'pay_way' => $orderInfo->pay_way,
                'pay_cost' => $orderInfo->total_cost,
                'dateline' => $orderInfo->pay_time
            ]);
        }

        $refundList = [];
        //获取退款信息数据
        $res = OrdersRefund::getInstance()->getListByConditions([
            'order_id' => $orderId
        ]);
        foreach ($res as $v) {
            $refundList[] = [
                'way' => BusinessConstants::PAY_STATUS_SET[$v->refund_way],
                'amount' => $v->refund_amount
            ];
        }

        $refund = OrdersRefund::getInstance()->getOneByField('order_id', $orderId);


        parent::renderSuccessJson([
            'order_id' => $orderId,
            'item_list' => $itemList,
            'part_list' => $partList,
            'order_status' => $orderInfo->order_status,
            'status' => BusinessConstants::ORDER_STATUS[$orderInfo->order_status],
            'pay_status' => $orderInfo->pay_status,
            'pay_state' => BusinessConstants::ORDER_PAY_STATUS[$orderInfo->pay_status],
            'vehicle_number' => $orderInfo->vehicle_number,
            'vehicle_owner' => $orderInfo->vehicle_owner,
            'wait_number' => $orderInfo->wait_number,
            'op_type' => array_map(function ($i) {
                return BusinessConstants::OP_ITEMS[$i];
            }, Orders::getInstance()->getOpTypeList($orderId)),
            'order_sn' => $orderInfo->order_sn,
            'vehicle_vin' => $orderInfo->vehicle_vin,
            'vehicle_color' => $orderInfo->vehicle_color,
            'vehicle_model' => $orderInfo->vehicle_model,
            'final_endtime' => OrdersItems::getInstance()->getQualityEndTime($orderId),
            'bill_operator' => Accounts::getInstance()->getUserNameByUid($orderInfo->bill_operator),
            'service_consultant' => Accounts::getInstance()->getUserNameByUid($orderInfo->service_consultant),
            'consultant_phone' => $orderInfo->consultant_phone,
            'total_times' => $orderInfo->total_times,
            'total_parts' => $orderInfo->total_parts,
            'times_cost' => $orderInfo->times_cost,
            'parts_cost' => $orderInfo->parts_cost,
            'other_cost' => $orderInfo->other_cost,
            'total_cost' => $orderInfo->total_cost,
            'barcode' => UtilFunction::getBarcode($orderInfo->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId),
            'model' => $orderInfo->brand_model,
            'brand' => ConfigBrand::getInstance()->getOneByField('id', $orderInfo->brand_id)->brand_name,
            'pay_way' => $orderInfo->pay_way ? BusinessConstants::PAY_STATUS_SET[$orderInfo->pay_way] : '',
            'pay_list' => $payList,
            'refund_list' => $refundList,
            'operators' => $operators,
            'refund_operator' => $refund ? Accounts::getInstance()->getUserNameByUid($refund->operator) : '',
            'refund_time' => $refund ? date('Y-m-d H:i:s', $refund->dateline) : 0
        ]);
    }


    public function getPayStatusAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $ret = Orders::getInstance()->getOneByField('id', $orderId);

        parent::renderSuccessJson([
            'status' => ($ret && $ret->pay_status == 2 && $ret->order_status == 6) ? 1 : 0
        ]);
    }

    public function refundAction()
    {
        $orderId = $this->getParameter('order_id');
        $refunds = $this->getParameter('refunds', []);
        $ret = Orders::getInstance()->getOneByField('id', $orderId);
        $this->mutexLock($orderId);

        if (!$ret || ($ret->pay_status != 2)) {
            static::errorThrow('未支付订单无法退款');
        }

        // 90天以内的可以退款
        if ($ret->pay_time < (time() - 90 * 3600 * 24)) {
            static::errorThrow('超过90天订单禁止操作退款!');
        }

        $sum = 0;
        foreach ($refunds as $v) {
            $sum += $v['cost'];
        }

        if (floatval($sum) !== floatval($ret->total_cost)) {
            static::errorThrow('退款总金额需等于付款总金额!');
        }

        BaseModel::getInstance()->beginTransaction();
        OrdersRefund::getInstance()->deleteByFields(['order_id' => $orderId]);
        foreach ($refunds as $v) {
            OrdersRefund::getInstance()->insertByFields([
                'order_id' => $orderId,
                'refund_way' => $v['way'],
                'refund_amount' => $v['cost'],
                'operator' => $this->uid,
                'dateline' => time()
            ]);
        }
        //设定退款
        Orders::getInstance()->updateByFields(['pay_status' => 3], [
            'id' => $orderId
        ]);
        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function accountListAction($settle = false)
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        $export = $this->getParameter('export', 0);

        $orderIds = [];
        $list = [];
        $total = 0;
        $sumTurnover = 0;
        $commissionRate = 0;
        $totalItemsAmount = 0;
        // 税率
        $taxRate = 1;
        $originTaxRate = 0;
        if ($tmp = Setting::getInstance()->getOneByField('skey', BusinessConstants::SETTING_KEY_TAXRATE)) {
            $originTaxRate = $tmp->sval ? $tmp->sval : 0;
            $taxRate = ($tmp->sval ? ((100 + $tmp->sval) / 100) : 1);
        }

        if ($beginTime && $endTime) {

            if (time() <= $endTime) {
                static::errorThrow('筛选范围不能大于当前时间!');
            }

            if ($export || $settle) {
                $pageOffset = 0;
                $pageSize = BusinessConstants::MAX_RETURN_SIZE;
            }

            $conditions = [
                'pay_status' => [1, 2],
                'pay_time>=' => $beginTime,
                'pay_time<=' => $endTime
            ];

            if ($settle && Commission::getInstance()->getOneByFields([
                    'order_id' => Orders::getInstance()->getAllListByOnField($conditions, 'id'),
                    'set_time>' => 0
                ])) {
                static::errorThrow('您筛选的区间存在已结算的订单，请重新设定时间筛选范围!');
            }

            $total = Orders::getInstance()->countListByConditions($conditions);
            $res = Orders::getInstance()->getListByConditions($conditions, 0, BusinessConstants::MAX_RETURN_SIZE)->toArray();

            foreach ($res as $v) {
                $sumTurnover += $v->total_cost;
                $orderIds[] = $v->id;
            }
            $list = array_slice($res, $pageOffset, $pageSize);

            $commission = CommissionConfig::getInstance()->getOneByFields(['min_turnover<=' => $sumTurnover, 'max_turnover>=' => $sumTurnover]);

            if ($commission) {
                $commissionRate = $commission->commission_rate;
            }

            if (empty($orderIds)) {
                static::errorThrow('没有需要核算的订单');
            }

            list($costData, $totalCosts, $totalTimes) = OrdersItems::getInstance()->getSummaryByOrderIds($orderIds);

            //计算工时总收入
            foreach ($totalCosts as $v) {
                $totalItemsAmount += $v;
            }

            $commissionList = Commission::getInstance()->getDataByOrderIds($orderIds);

            foreach ($list as &$v) {
                $v->order_times = isset($totalTimes[$v->id]) ? $totalTimes[$v->id] : 0;
                $v->order_cost = isset($totalCosts[$v->id]) ? $totalCosts[$v->id] : 0;
                $v->type = implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn));
                $commission_cost = 0;
                if (isset($costData[$v->id])) {
                    $tmp = [];
                    $uNames = [];
                    $v->is_check = 0;
                    if (isset($commissionList[$v->id])) {
                        // 已设置了提成百分比的数据
                        foreach ($commissionList[$v->id] as $opType => $val) {
                            foreach ($val as $vv) {
                                // fix 返工导致commision表数据多余的问题，进行清理
                                if (!isset($costData[$v->id][$vv->op_type][$vv->item_id])) {
                                    Commission::getInstance()->deleteByFields(
                                        [
                                            'order_id' => $v->id,
                                            'item_id' => $vv->item_id,
                                            'op_type' => $vv->op_type
                                        ]
                                    );
                                    continue;
                                }

                                $uNames[$vv->account_id] = $vv->username;
                                if (isset($tmp[$vv->account_id])) {
                                    $tmp[$vv->account_id] += round($costData[$v->id][$vv->op_type][$vv->item_id]->final_price * $commissionRate / $taxRate * $vv->percentage,
                                        2);
                                } else {
                                    $tmp[$vv->account_id] = round($costData[$v->id][$vv->op_type][$vv->item_id]->final_price * $commissionRate / $taxRate * $vv->percentage,
                                        2);
                                }

                                $commission_cost += $tmp[$vv->account_id];

                                if ($vv->set_time) {
                                    $v->is_check = 1;
                                }
                            }
                        }
                    } else {
                        Commission::getInstance()->deleteByFields(['order_id' => $v->id]);
                        // 未设置提成百分比的数据自动计算填充
                        foreach ($costData[$v->id] as $opType => $itemV) {
                            foreach ($itemV as $itemId => $itemVal) {
                                $operators = explode(',', $itemVal->operators);
                                $percentage = round(1 / count($operators), 4);
                                foreach ($operators as $accountId) {
                                    $uNames[$accountId] = Accounts::getInstance()->getUserNameByUid($accountId);
                                    Commission::getInstance()->insertByFields([
                                        'order_id' => $v->id,
                                        'account_id' => $accountId,
                                        'item_id' => $itemId,
                                        'op_type' => $opType,
                                        'percentage' => $percentage,
                                        'create_time' => $itemVal->dateline
                                    ]);

                                    if (isset($tmp[$accountId])) {
                                        $tmp[$accountId] += round($itemVal->final_price * $commissionRate / $taxRate * $percentage,
                                            2);
                                    } else {
                                        $tmp[$accountId] = round($itemVal->final_price * $commissionRate / $taxRate * $percentage,
                                            2);
                                    }

                                    $commission_cost += $tmp[$accountId];
                                }
                            }
                        }

                    }

                    foreach ($uNames as $uid => $username) {
                        $v->commission_list[] = [
                            'operator' => $username,
                            'commission' => $tmp[$uid]
                        ];
                    }

                } else {
                    $v->commission_list = [];
                }

                $v->commission_cost = $commission_cost;
            }
            unset($v);
        }

        if ($settle && $orderIds) {
            list($costData, $totalCosts, $totalTimes) = OrdersItems::getInstance()->getSummaryByOrderIds($orderIds);
            $commissionList = Commission::getInstance()->getDataByOrderIds($orderIds);
            foreach ($list as $v) {
                if (isset($commissionList[$v->id])) {
                    foreach ($commissionList[$v->id] as $opType => $val) {
                        foreach ($val as $vv) {
                            if ($vv->set_time) {
                                continue;
                            }
                            Commission::getInstance()->updateByFields([
                                'set_time' => time(),
                                'commission_rate' => $commissionRate,
                                'tax_rate' => $taxRate,
                                'fee' => round($costData[$v->id][$vv->op_type][$vv->item_id]->final_price * $commissionRate / $taxRate * $vv->percentage,
                                    2)
                            ], ['id' => $vv->id]);
                        }
                    }
                }
            }

            parent::renderSuccessJson();

            return;
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('薪水核算表');
            $fields = [
                'order_sn' => '订单号',
                'type' => '业务类型',
                'order_times' => '总工时',
                'order_cost' => '总工时费',
                'commission_list' => '维修人员提成',
                'is_check' => '核算状态'
            ];

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->getColumnDimension(chr(ord('A') + $k))->setWidth(20);
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }

            // 初始化下拉选项
            foreach ($list as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    if (is_array($v->$f)) {
                        $workSheet->getStyleByColumnAndRow($i, (2 + $k))->getAlignment()->setWrapText(true);
                    }
                    if ($f == 'is_check') {
                        $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v->$f ? '已核算' : '未核算', DataType::TYPE_STRING);
                    } else {
                        $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), is_array($v->$f) ? implode("\r\n", array_map(function ($o) {
                            return $o['operator'] . ' : ' . $o['commission'];
                        }, $v->$f)) : $v->$f, DataType::TYPE_STRING);
                    }
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="薪水核算' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'turnover' => $sumTurnover,
            'rate' => $commissionRate,
            'tax_rate' => $taxRate,
            'total_commission' => $totalItemsAmount * $commissionRate,
            'total_notax_commission' => round($totalItemsAmount * $commissionRate / ($taxRate ? ((100 + $taxRate) / 100) : 1), 2),
            'page' => UtilFunction::renderPage($total, $curPage),
        ]);
    }

    public function accountingAction()
    {
        $this->accountListAction(true);
    }
}