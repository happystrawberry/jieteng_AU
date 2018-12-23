<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/11
 * Time: 17:26
 */

namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\AccountsGroupItems;
use App\Models\Commission;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\WorkTask;
use App\Models\WorkTaskPeople;

class DispatchController extends ApiBaseController
{
    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $orderStatus = $this->getParameter('order_status', '');

        $conditions = [];
        if ($orderStatus) {
            $conditions['order_status'] = explode(',', $orderStatus);
        } else {
            $conditions['order_status'] = [2, 3];
        }

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
                    'wait_number' => $v->wait_number,
                    'vehicle_number' => $v->vehicle_number,
                    'vehicle_model' => $v->vehicle_model,
                    'plan_endtime' => $v->plan_endtime,
                    'order_status' => $v->order_status,
                    'status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'service_groups' => Orders::getInstance()->getOpTypeNameGroupByOrderSn($v->order_sn),
                    'dispatch_list' => WorkTask::getInstance()->getTaskListByOrderId($v->id),
                    'arrive_time' => $v->arrive_time,
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([2, 3], false, false, $beginTime, $endTime)
        ]);
    }


    public function getServiceGroupAction()
    {
        $list = $this->getUrlCache();

        if (!$list) {
            $list = [];
            foreach (BusinessConstants::OP_ITEMS as $k => $v) {
                $list[] = [
                    'type' => $k,
                    'name' => $v,
                    'users' => AccountsGroupItems::getInstance()->getAccountListByOpType($k),
                    'teams' => AccountsGroupItems::getInstance()->getGroupListByTypeId($k)
                ];
            }

            $this->setUrlCache($list);
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function saveWorkTaskAction()
    {
        $orderId = $this->getParameter('order_id');
        $dispatch = $this->getParameter('dispatch');

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => [2, 3, 4]]);

        if (!$ret) {
            static::errorThrow('此订单无法设置调度信息');
        }

        $typeList = Orders::getInstance()->getOpTypeList($orderId);

        if (count($typeList) != count($dispatch)) {
            static::errorThrow('请配置完整调度信息');
        }

        $values = [];
        $accounts = [];
        foreach ($dispatch as $v) {
            if (!in_array($v['op_type'], $typeList)) {
                static::errorThrow('调度信息配置有误');
            }

            if (!$v['target_account'] && !$v['target_team']) {
                static::errorThrow('请指派' . BusinessConstants::OP_ITEMS[$v['op_type']] . '调度人员或班组');
            }
            $accounts[] = $v['target_account'];
            $v['dateline'] = time();
            $v['order_id'] = $orderId;
            $values[] = $v;

            if (BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$v['op_type']] == 'people') {
                OrdersItems::getInstance()->updateByFields(['operators' => $v['target_account']], ['op_type' => $v['op_type'], 'order_id' => $orderId]);
                Commission::getInstance()->deleteByFields(['order_id' => $orderId, 'op_type' => $v['op_type']]);
                $itemIds = OrdersItems::getInstance()->getAllListByOnField(['order_id' => $orderId, 'op_type' => $v['op_type']], 'id');
                foreach ($itemIds as $iids) {
                    Commission::getInstance()->insertByFields([
                        'order_id' => $orderId,
                        'account_id' => $v['target_account'],
                        'item_id' => $iids,
                        'op_type' => $v['op_type'],
                        'percentage' => 1,
                        'create_time' => time()
                    ]);
                }
            }
        }

        WorkTask::getInstance()->deleteByFields(['order_id' => $orderId]);
        if (!WorkTask::getInstance()->batchInsertByFields($values)) {
            static::errorThrow('设置调度失败');
        }

        Orders::getInstance()->updateByFields(['order_status' => 3], ['id' => $orderId]);

        WorkTaskPeople::getInstance()->updateAccountsByOrderId($orderId);

        parent::renderSuccessJson();
    }
}