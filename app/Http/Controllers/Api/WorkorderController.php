<?php
/**
 *
 *
 * @since   2018/11/2 10:08
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\Commission;
use App\Models\FeedBack;
use App\Models\FeedbackQuestions;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\OrdersPay;
use App\Models\OrdersRefund;
use App\Models\Parts;
use App\Models\WorkTask;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WorkorderController extends ApiBaseController
{
    public function listAction()
    {

        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $orderStatus = $this->getParameter('order_status', '');
        $export = $this->getIntegerParameter('export', 0);
        $conditions = [];

        if ($orderStatus !== '') {
            $orderStatus = explode(',', $orderStatus);
            $tmp = [];
            foreach ($orderStatus as $os) {
                if ($os >= 80 && $os < 90) {
                    $conditions['pay_status'][] = substr($os, 1);
                    $tmp = array_merge($tmp, [5, 6]);
                } else {
                    $tmp = array_merge($tmp, [$os]);
                }
            }
            $conditions['order_status'] = array_unique($tmp);
        } else {
            $conditions['order_status'] = [0, 1, 2, 3, 4, 5, 6, 7, 94, 95, 96, 97, 99];
        }

        $query = $this->getParameter('query', '');

        $serviceConsultant = $this->getParameter('service_consultant', '');

        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);

        if ($query) {
            $conditions['%%'] = [
                'vehicle_number' => $query,
                'order_sn' => $query,
                'phone' => $query,
                'contact_name' => $query
            ];
        }

        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
        }

        if ($serviceConsultant) {
            $conditions['service_consultant'] = Accounts::getInstance()->getOneByField('username', $serviceConsultant)->id;
        }

        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $totalIncome = Orders::getInstance()->sumIncome($conditions);

        $list = [];
        $total = Orders::getInstance()->countListByConditions($conditions);
        if ($total) {
            $res = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize, 'id', 'DESC', ['*']);
            foreach ($res as $v) {
                $list[] = [
                    'order_id' => $v->id,
                    'order_sn' => $v->order_sn,
                    'wait_number' => $v->wait_number,
                    'service_consultant' => Accounts::getInstance()->getUserNameByUid($v->service_consultant),
                    'arrive_time' => $v->arrive_time,
                    'plan_endtime' => $v->plan_endtime,
                    'contact_name' => $v->contact_name,
                    'order_status' => $v->order_status,
                    'status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'vehicle_number' => $v->vehicle_number,
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'phone' => $v->phone,
                    'arrive_datetime' => date('Y-m-d H:i:s', $v->arrive_time),
                    'income' => ($v->pay_status == 2) ? $v->total_cost : 0,
                    'pay_status' => BusinessConstants::ORDER_PAY_STATUS[$v->pay_status]
                ];
            }
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('订单表');
            $fields = [
                'order_sn' => '订单号',
                'status' => '订单状态',
                'arrive_datetime' => '到店时间',
                'contact_name' => '联系人',
                'phone' => '电话',
                'vehicle_number' => '车牌号',
                'service_items' => '业务类型',
                'service_consultant' => '服务顾问',
                'income' => '实收金额',
                'pay_status' => '支付状态'
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
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k),
                        in_array($f, ['arrive_time', 'plan_endtime']) ? date('Y-m-d H:i:s', $v[$f]) : $v[$f], DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="订单数据' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        $uids = Orders::getInstance()->getAllListByOnField(['order_status' => [0, 1, 2, 5, 6, 94, 96]], 'service_consultant');
        $unames = Accounts::getInstance()->getUserNameByUids($uids);
        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([0, 1, 2, 3, 4, 7, 94, 95, 96, 97, 99], true, true, $beginTime, $endTime),
            'sc_list' => $unames,
            'total_income' => $totalIncome
        ]);
    }

    public function viewOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $order = Orders::getInstance()->getOneByField('id', $orderId);

        if (!$order) {
            static::errorThrow('数据不存在');
        }

        $order->service_consultant_name = Accounts::getInstance()->getOneByField('id',
            $order->service_consultant)->username;

        $order->check_images = array_map(function ($i) {
            if ((strpos($i, 'http://') !== false) || (strpos($i, 'https://') !== false)) {
                return $i;
            }

            return $this->request->getSchemeAndHttpHost() . $i;
        }, array_filter(explode(',', $order->check_images)));

        $order->barcode = UtilFunction::getBarcode($order->order_sn);
        $order->status = BusinessConstants::ORDER_STATUS[$order->order_status];

        //计算项目进度
        $progress = [];
        $res = WorkTask::getInstance()->getListByConditions(['order_id' => $orderId]);
        foreach ($res as $v) {
            $progress[] = [
                'item' => isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '',
                'status' => $v->is_redo ? '返工' : ($v->is_start ? ($v->is_done ? '已完成' : '已开工') : ('未开工'))
            ];
        }

        // 质检详情
        $itemList = OrdersItems::getInstance()->getListByConditions(['order_id' => $orderId, 'enable' => 1], 0, BusinessConstants::MAX_RETURN_SIZE);
        $partList = OrdersParts::getInstance()->getListByConditions(
            [
                'order_id' => $orderId,
                'parts_status' => 1
            ], 0, BusinessConstants::MAX_RETURN_SIZE
        );
        $allOperators = [];
        $opNames = WorkTask::getInstance()->getOperatorNameByOrderId($orderId);
        foreach ($itemList as &$v) {
            $allOperators[$v->op_type] = [
                'type' => isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '',
                'operators' => isset($opNames[$v->op_type]) ? $opNames[$v->op_type] : ''
            ];
            $v->operator = isset($opNames[$v->op_type]) ? $opNames[$v->op_type] : '';
            $v->status = BusinessConstants::OP_ORDER_QC_STATUS[$v->quality_status];
            $v->type = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
        }
        unset($v);
        foreach ($partList as &$v) {
            $parts = Parts::getInstance()->getOneByField('sn', $v->parts_sn);
            $v->status = BusinessConstants::OP_ORDER_QC_STATUS[$v->quality_status];
            $v->location = $parts ? $parts->parts_location : '';
            $v->target_model = $parts ? $parts->target_model : '';
        }
        unset($v);

        // 薪水统计
        $salary = [];
        list($costData, $totalCosts, $totalTimes) = OrdersItems::getInstance()->getSummaryByOrderId($orderId);
        $commissions = Commission::getInstance()->getDataByOrderId($orderId);

        foreach ($commissions as $opType => $v) {
            foreach ($v as $vv) {
                $operators = isset($costData[$opType][$vv->item_id]) ? explode(',', $costData[$opType][$vv->item_id]->operators) : [];
                $times = isset($costData[$opType][$vv->item_id]) ? ($costData[$opType][$vv->item_id]->item_times / count($operators)) : 0;
                $salary[] = [
                    'op_type' => BusinessConstants::OP_ITEMS[$opType],
                    'username' => $vv->username,
                    'times' => $times,
                    'ratio' => ($vv->percentage * 100) . '%',
                    'fee' => $vv->fee,
                    'item_name' => OrdersItems::getInstance()->getOneByField('id', $vv->item_id)->item_name,
                    'total_price' => isset($costData[$opType][$vv->item_id]) ? $times * $costData[$opType][$vv->item_id]->item_cost : 0
                ];
            }
        }

        $questions = FeedbackQuestions::getInstance()->getQuestionListByOrderId($orderId);
        foreach ($questions as &$q) {
            $q->options = json_decode($q->options);
        }

        $order->bill_operator = Accounts::getInstance()->getUserNameByUid($order->bill_operator);
        $order->done_time = WorkTask::getInstance()->ifAllTaskDone($orderId) ? WorkTask::getInstance()->getFinishTime($orderId) : 0;

        //获取支付信息数据
        $payWays = [];
        $res = OrdersPay::getInstance()->getListByConditions([
            'order_id' => $orderId
        ]);
        if (count($res)) {
            foreach ($res as $v) {
                $payWays[] = BusinessConstants::PAY_STATUS_SET[$v->pay_way];
            }
        } elseif ($order->pay_way) {
            $payWays[] = BusinessConstants::PAY_STATUS_SET[$order->pay_way];
        }
        $order->pay_ways = $payWays;


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
            'info' => $order,
            'service_items' => Orders::getInstance()->getOpTypeNameByOrderSn($order->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId),
            'progress' => $progress,
            'item_list' => $itemList,
            'part_list' => $partList,
            'feedback' => FeedBack::getInstance()->getOneByFields(['order_id' => $orderId]),
            'questions' => $questions,
            'salary' => $salary,
            'operators' => array_values($allOperators),
            'refund_list' => $refundList,
            'refund_operator' => $refund ? Accounts::getInstance()->getUserNameByUid($refund->operator) : '',
            'refund_time' => $refund ? date('Y-m-d H:i:s', $refund->dateline) : 0
        ]);
    }
}