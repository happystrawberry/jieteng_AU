<?php
/**
 *
 *
 * @since   2018/6/13 21:48
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\BaseModel;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\WorkTask;

class QualityController extends ApiBaseController
{
    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $orderStatus = $this->getParameter('order_status', '');

        $conditions = [];
        if ($orderStatus) {
            $conditions['order_status'] = explode(',', $orderStatus);;
        } else {
            $conditions['order_status'] = [4, 5];
        }

        if (in_array(98, $conditions['order_status'])) {
            $conditions['need_wash'] = 1;
            $conditions['wash_status'] = 0;
            $conditions['order_status'] = [4, 5];
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
                    'order_status' => ($v->order_status == 4) ? '待质检' : BusinessConstants::ORDER_STATUS[$v->order_status],
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'need_wash' => $v->need_wash,
                    'end_time' => OrdersItems::getInstance()->getQualityEndTime($v->id),
                    'wash_status' => $v->wash_status,
                    'arrive_time' => $v->arrive_time,
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([4, 5], true, false, $beginTime, $endTime)
        ]);
    }

    public function setWashDoneAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => [4, 5], 'wash_status' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单数据不存在');
        }

        Orders::getInstance()->updateByFields(['wash_status' => 1], ['id' => $orderId]);

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

        $opNames = WorkTask::getInstance()->getOperatorNameByOrderId($orderId);

        foreach ($itemList as &$v) {
            $v->operator = $opNames[$v->op_type];
            $v->status = BusinessConstants::OP_ORDER_QC_STATUS[$v->quality_status];
        }
        unset($v);
        parent::renderSuccessJson([
            'item_list' => $itemList,
            'part_list' => $partList,
            'order_status' => BusinessConstants::ORDER_STATUS[$orderInfo->order_status],
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
            'plan_endtime' => $orderInfo->plan_endtime,
            'final_endtime' => OrdersItems::getInstance()->getQualityEndTime($orderId),
            'barcode' => UtilFunction::getBarcode($orderInfo->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId)
        ]);
    }

    public function setStatusAction()
    {
        $id = $this->getParameter('id');
        $pass = $this->getIntegerParameter('pass', 1);
        $type = $this->getParameter('type');

        $ids = explode(',', $id);

        BaseModel::getInstance()->beginTransaction();
        foreach ($ids as $id) {
            $ret = false;
            switch ($type) {
                case 'item':
                    $ret = OrdersItems::getInstance()->getOneByField('id', $id);
                    break;
                case 'part':
                    $ret = OrdersParts::getInstance()->getOneByField('id', $id);
                    break;
                default:
                    static::errorThrow('type参数不正确');
            }

            if (!$ret) {
                static::errorThrow('数据不存在');
            }
            $orderId = $ret->order_id;
            $opType = $ret->op_type;

            $changeStatus = 4;
            switch ($type) {
                case 'item':
                    //检查当前项目是否已经完工，没有完工禁止质检
                    if (!WorkTask::getInstance()->countListByConditions(['order_id' => $orderId, 'op_type' => $ret->op_type, 'is_done' => 1])) {
                        static::errorThrow('没有完工项目禁止设定质检状态');
                    }

                    OrdersItems::getInstance()->updateByFields(['quality_status' => ($pass ? 1 : 2), 'quality_time' => time()], ['id' => $id]);
                    $changeStatus = 95;
                    break;
                case 'part':
                    //检查是否已经发货，如果没有发货就不能质检合格
                    if ($ret->parts_status != 1) {
                        static::errorThrow('未发货材料不能质检');
                    }

                    OrdersParts::getInstance()->updateByFields(['quality_status' => ($pass ? 1 : 2), 'quality_time' => time()], ['id' => $id]);
                    $changeStatus = 94;
                    break;
            }

            if (!$pass) {
                //不通过就是返工
                WorkTask::getInstance()->updateByFields([
                    'is_done' => 0,
                    'done_time' => 0,
                    'is_redo' => 1,
                    'redo_time' => time()
                ], ['order_id' => $orderId, 'op_type' => $opType]);

                Orders::getInstance()->updateByFields(['order_status' => $changeStatus], ['id' => $ret->order_id]);
            }

            // 如果维修项目都ok，但是材料质检不合格，就设定94
            if (!OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'quality_status' => [0, 2]])
                && OrdersParts::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'quality_status' => 2])
            ) {
                Orders::getInstance()->updateByFields(['order_status' => 94], ['id' => $ret->order_id]);
            }

            //检查如果都质检ok，才能算已完工
            if (!OrdersItems::getInstance()->countListByConditions(['quality_status' => [0, 2], 'order_id' => $ret->order_id])
                && !OrdersParts::getInstance()->countListByConditions(['quality_status' => [0, 2], 'parts_status' => [0, 1], 'order_id' => $ret->order_id])
                && !OrdersParts::getInstance()->countListByConditions(['parts_status' => 0, 'order_id' => $ret->order_id])
            ) {
                Orders::getInstance()->updateByFields(['order_status' => 5], ['id' => $ret->order_id, 'order_status' => [4, 94]]);

                WorkTask::getInstance()->updateByFields([
                    'is_redo' => 0,
                    'redo_time' => 0
                ], ['order_id' => $orderId, 'op_type' => $opType]);
            }
        }
        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }
}