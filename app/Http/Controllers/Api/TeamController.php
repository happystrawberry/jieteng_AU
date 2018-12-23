<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/12
 * Time: 10:34
 */

namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\AccountGroups;
use App\Models\Accounts;
use App\Models\AccountsGroupItems;
use App\Models\BaseModel;
use App\Models\Commission;
use App\Models\ConfigBrand;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\WorkTask;
use App\Models\WorkTaskPeople;

class TeamController extends ApiBaseController
{
    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $orderStatus = $this->getParameter('order_status', '');

        $conditions = [];
        if ($orderStatus) {
            $conditions['order_status'] = explode(',', $orderStatus);;
        } else {
            $conditions['order_status'] = [3, 4, 5, 95, 99];
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
                    'status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'order_status' => $v->order_status,
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'dispatch_list' => WorkTask::getInstance()->getTaskListByOrderId($v->id),
                    'all_done' => WorkTask::getInstance()->ifAllTaskDone($v->id),
                    'service_groups' => Orders::getInstance()->getOpTypeNameGroupByOrderSn($v->order_sn),
                    'arrive_time' => $v->arrive_time,
                    'brand' => ConfigBrand::getInstance()->getOneByField('id', $v->brand_id)->brand_name,
                    'model' => $v->brand_model
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([3, 4, 5, 95, 99], false, false, $beginTime, $endTime)
        ]);
    }

    public function redoAction()
    {
        $orderId = $this->getIntegerParameter('order_id');
        $opType = $this->getIntegerParameter('op_type');

        OrdersItems::getInstance()->updateByFields([
            'quality_status' => 2
        ], ['order_id' => $orderId, 'op_type' => $opType]);

        parent::renderSuccessJson();
    }

    public function getDispatchListAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        parent::renderSuccessJson([
            'list' => WorkTask::getInstance()->getTaskListByOrderId($orderId)
        ]);
    }

    public function setStartStatusAction()
    {
        $id = $this->getIntegerParameter('id');

        BaseModel::getInstance()->beginTransaction();

        $ret = WorkTask::getInstance()->getOneByField('id', $id);

        if (!in_array($ret->op_type, $this->groupOpTypes)) {
            static::errorThrow('你没有权限操作未被授权的业务类型');
        }

        //判断是否没有分配人员，如果没有分配禁止操作
        if (empty($ret->target_account) && OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'op_type' => $ret->op_type])) {
            static::errorThrow('未分配人员禁止设定完工状态!');
        }

        if ($ret && ($orderInfo = Orders::getInstance()->getOneByFields(['id' => $ret->order_id, 'order_status' => [3, 4]]))) {
            if (!WorkTask::getInstance()->updateByFields(['is_start' => 1, 'start_time' => time()], ['id' => $id, 'is_start' => 0])) {
                static::errorThrow('设置开工状态失败#1');
            }

            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $ret->order_id, 'order_status' => 3]);

        } else {
            static::errorThrow('设置开工状态失败#2');
        }

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function setDoneStatusAction()
    {
        $id = $this->getIntegerParameter('id');
        BaseModel::getInstance()->beginTransaction();
        //检查这个项目的材料是否已经全部领取完毕
        $ret = WorkTask::getInstance()->getOneByField('id', $id);
        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        if (!in_array($ret->op_type, $this->groupOpTypes)) {
            static::errorThrow('你没有权限操作未被授权的业务类型');
        }

        //判断是否没有分配人员，如果没有分配禁止操作
        if (empty($ret->target_account) && OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'op_type' => $ret->op_type])) {
            static::errorThrow('未分配人员禁止设定完工状态!');
        }

        //暂停状态禁止完工
        $order = Orders::getInstance()->getOneByField('id', $ret->order_id);
        if (!in_array($order->order_status, [4, 95])) {
            static::errorThrow('当前禁止修改!');
        }

        if (OrdersParts::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'op_type' => $ret->op_type, 'parts_status' => 0])) {
            static::errorThrow('当前项目有未领取的配件材料，禁止设定完工状态!');
        }

        // 没有设置提成比例的项目禁止设定完工状态
        if (OrdersItems::getInstance()->getOneByFields([
                'order_id' => $ret->order_id,
                'op_type' => $ret->op_type
            ]) && !Commission::getInstance()->getOneByFields(['order_id' => $ret->order_id, 'op_type' => $ret->op_type])) {
            static::errorThrow('没有设定提成比例的业务无法设定完工状态!');
        }

        if (!WorkTask::getInstance()->updateByFields(
            ['is_done' => 1, 'done_time' => time(), 'is_redo' => 0, 'redo_time' => 0],
            ['id' => $id, 'is_done' => 0, 'is_start' => 1])) {
            static::errorThrow('设置失败');
        }

        OrdersItems::getInstance()->updateByFields([
            'quality_status' => 0,
            'quality_time' => 0
        ], [
            'order_id' => $ret->order_id,
            'op_type' => $ret->op_type,
        ]);

        if (!OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'quality_status' => 2])) {
            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $ret->order_id, 'order_status' => 95]);
        }
        BaseModel::getInstance()->commitTransaction();
        parent::renderSuccessJson();
    }

    public function getOperatorsAction()
    {
        $teamId = $this->getParameter('team_id');
        $itemId = $this->getParameter('item_id');
        $orderId = $this->getParameter('order_id');

        $commission = Commission::getInstance()->getCommissionByItemId($orderId, $itemId);

        $res = Accounts::getInstance()->getAllListByFields([
            'id' => array_unique(AccountGroups::getInstance()->getAllListByOnField(['group_id' => $teamId], 'uid'))
        ], ['id', 'username']);

        foreach ($res as &$v) {
            $v->commission = isset($commission[$v->id]) ? $commission[$v->id] : 0;
        }
        unset($v);
        parent::renderSuccessJson([
            'list' => $res
        ]);
    }

    public function getOperatorsByOpTypeAction()
    {
        $opType = $this->getParameter('op_type');

        parent::renderSuccessJson([
            'list' => AccountsGroupItems::getInstance()->getAccountListByOpType($opType)
        ]);
    }

    public function setOperatorAction()
    {
        $id = $this->getIntegerParameter('id');
        $operators = $this->getParameter('operators');
        $itemId = $this->getParameter('item_id');

        BaseModel::getInstance()->beginTransaction();

        $ret = WorkTask::getInstance()->getOneByField('id', $id);

        if (!$ret || !$ret->target_team) {
            static::errorThrow('没有指定班组无法设定具体实施者');
        }

        if ($ret->is_done) {
            static::errorThrow('已完工订单禁止修改实施者!');
        }

        //检查订单状态
        $orderInfo = Orders::getInstance()->getOneByField('id', $ret->order_id);

        if ($orderInfo->order_status < 0 || ($orderInfo->order_status >= 6 && ($orderInfo->order_status != 95))) {
            static::errorThrow('当前订单状态无法设置施工者');
        }
        //检查操作者是否合法
        $commission = $this->getParameter('commission', []); // 获取提成数据
        $accounts = explode(',', $operators);

        if (!is_array($commission) || (count($commission) != count($accounts)) || (floatval(array_sum(array_map(function ($v) {
                    return sprintf('%.2f', $v);
                }, $commission))) !== (float)100)) {
            static::errorThrow('请确保每位维修人员提成比例设置正确,并保证提成比例总和为100%!');
        }

        // 如果已经结算的订单禁止修改
        if (Commission::getInstance()->getOneByFields(['order_id' => $ret->order_id, 'set_time>' => 0])) {
            static::errorThrow('已结算订单禁止修改!');
        }

        foreach ($accounts as $idx => $uid) {
            $account = Accounts::getInstance()->getOneByField('id', $uid);
            if (!$account || (!in_array($ret->target_team, array_filter(explode(',', $account->groupid))))) {
                static::errorThrow('施工者设置有误');
            }

            Commission::getInstance()->deleteByFields(['order_id' => $ret->order_id, 'account_id' => $uid, 'item_id' => $itemId, 'op_type' => $ret->op_type]);
            // 记录施工者提成设置记录
            Commission::getInstance()->insertByFields([
                'order_id' => $ret->order_id,
                'account_id' => $uid,
                'item_id' => $itemId,
                'op_type' => $ret->op_type,
                'percentage' => $commission[$idx] / 100,
                'create_time' => time()
            ]);
        }

        if (!WorkTask::getInstance()->updateByFields(['target_account' => $operators, 'dateline' => time()], ['id' => $id])) {
            static::errorThrow('设置失败');
        }

        WorkTaskPeople::getInstance()->updateAccountsByOrderId($ret->order_id);

        OrdersItems::getInstance()->updateByFields(['operators' => $operators], ['id' => $itemId]);

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function viewOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status>' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单数据不存在');
        }

        list($payments, $itemList) = WorkTask::getInstance()->getPaymentsByOrderId($orderId);

        $partsList = OrdersParts::getInstance()->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);

        foreach ($partsList as &$v) {
            $v->status = BusinessConstants::ORDER_PARTS_STATUS[$v->parts_status];
        }

        parent::renderSuccessJson([
            'order_id' => $orderId,
            'payments' => array_values($payments),
            'items_list' => $itemList,
            'parts_list' => $partsList,
            'status' => (WorkTask::getInstance()->ifAllTaskDone($orderId) && $orderInfo->order_status == 4) ? '待质检' : BusinessConstants::ORDER_STATUS[$orderInfo->order_status],
            'order_status' => $orderInfo->order_status,
            'vehicle_number' => $orderInfo->vehicle_number,
            'vehicle_owner' => strval($orderInfo->vehicle_owner),
            'wait_number' => $orderInfo->wait_number,
            'op_type' => array_map(function ($i) {
                return BusinessConstants::OP_ITEMS[$i];
            }, Orders::getInstance()->getOpTypeList($orderId)),
            'order_sn' => $orderInfo->order_sn,
            'vehicle_vin' => $orderInfo->vehicle_vin,
            'vehicle_color' => $orderInfo->vehicle_color,
            'vehicle_model' => $orderInfo->vehicle_model,
            'model' => $orderInfo->brand_model,
            'brand' => ConfigBrand::getInstance()->getOneByField('id', $orderInfo->brand_id)->brand_name,
            'plan_endtime' => $orderInfo->plan_endtime,
            'barcode' => UtilFunction::getBarcode($orderInfo->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId),
            'done_time' => WorkTask::getInstance()->ifAllTaskDone($orderId) ? WorkTask::getInstance()->getFinishTime($orderId) : 0,
            'contact_name' => $orderInfo->contact_name
        ]);
    }

    public function resumeTaskAction()
    {
        $id = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByField('id', $id);

        if (!$orderInfo || $orderInfo->order_status != 99) {
            static::errorThrow('可恢复订单不存在');
        }

        if (!WorkTask::getInstance()->countListByConditions(['order_id' => $id, 'is_redo<>' => 0])) {
            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $id, 'order_status' => 99]);
        } else {
            Orders::getInstance()->updateByFields(['order_status' => 95], ['id' => $id, 'order_status' => 99]);
        }

        parent::renderSuccessJson();
    }

    public function pauseTaskAction()
    {
        $id = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByField('id', $id);

        if (!$orderInfo || !in_array($orderInfo->order_status, [4, 95])) {
            static::errorThrow('可暂停订单不存在');
        }

        Orders::getInstance()->updateByFields(['order_status' => 99], ['id' => $id, 'order_status' => [4, 95]]);

        parent::renderSuccessJson();
    }

    public function resetToDispatchAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $ret = Orders::getInstance()->getOneByField('id', $orderId);

        if (!$ret || !in_array($ret->order_status, [3, 4])) {
            static::errorThrow('只有已调度订单才能重置调度任务!');
        }

        Orders::getInstance()->updateByFields(['order_status' => 2], ['id' => $orderId, 'order_status' => [3, 4]]);
        WorkTaskPeople::getInstance()->deleteByFields(['order_id' => $orderId]);
        WorkTask::getInstance()->deleteByFields(['order_id' => $orderId]);
        Commission::getInstance()->deleteByFields(['order_id' => $orderId]);
        parent::renderSuccessJson();
    }
}