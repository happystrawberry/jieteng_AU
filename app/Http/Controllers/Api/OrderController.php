<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/7
 * Time: 16:16
 */

namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\BaseModel;
use App\Models\Commission;
use App\Models\Items;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\Parts;
use App\Models\PartsLog;
use App\Services\OrderService;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class OrderController extends ApiBaseController
{
    private function validOrderId($id)
    {
        $ret = Orders::getInstance()->getOneByFields(['id' => $id, 'order_status<>' => 0]);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        if (in_array($ret->order_status, [6, 7]) || in_array($ret->pay_status, [1, 2])) {
            static::errorThrow('已结算订单不能修改');
        }

        return $ret;
    }

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
                    $conditions['pay_status'] = substr($os, 1);
                    $tmp = array_merge($tmp, [5, 6]);
                } else {
                    $tmp = array_merge($tmp, [$os]);
                }
            }
            $conditions['order_status'] = array_unique($tmp);
        } else {
            $conditions['order_status'] = [0, 1, 2, 5, 6, 94, 96];
        }

        $vehicleNumber = $this->getParameter('vehicle_number', '');
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        $orderSn = $this->getParameter('order_sn', '');
        $serviceConsultant = $this->getParameter('service_consultant', '');
        $phone = $this->getParameter('phone', '');
        if ($vehicleNumber) {
            $conditions['%vehicle_number%'] = $vehicleNumber;
        }
        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
        }
        if ($orderSn) {
            $conditions['%order_sn%'] = $orderSn;
        }

        if ($phone) {
            $conditions['phone'] = $phone;
        }

        if ($serviceConsultant) {
            $conditions['service_consultant'] = Accounts::getInstance()->getOneByField('username', $serviceConsultant)->id;
        }

        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $list = [];
        $total = Orders::getInstance()->countListByConditions($conditions);
        if ($total) {
            $res = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize, 'id', 'DESC');
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
                    'phone' => $v->phone
                ];
            }
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('接单表');
            $fields = [
                'order_sn' => '订单号',
                'vehicle_number' => '车牌号',
                'contact_name' => '联系人',
                'phone' => '电话',
                'status' => '订单状态',
                'service_items' => '业务类型',
                'service_consultant' => '服务顾问',
                'arrive_time' => '到店时间',
                'plan_endtime' => '预计完工时间',
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
            header('Content-Disposition: attachment;filename="接单数据' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        $uids = Orders::getInstance()->getAllListByOnField(['order_status' => [0, 1, 2, 5, 6, 94, 96]], 'service_consultant');
        $unames = Accounts::getInstance()->getUserNameByUids($uids);

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([0, 1, 2, 94, 96], false, true, $beginTime, $endTime),
            'sc_list' => $unames
        ]);
    }

    public function saveOrderAction()
    {
        $id = $this->getParameter('id', 0);
        $vehicleNumber = $this->getParameter('vehicle_number');
        $vehicleVin = $this->getParameter('vehicle_vin');
        $brandId = $this->getParameter('brand_id');
        $model = $this->getParameter('model');
        $vehicleModel = $this->getParameter('vehicle_model');
        $vehicleColor = $this->getParameter('vehicle_color');
        $modelClass = $this->getParameter('model_class');
        $buyTime = $this->getParameter('buy_time', 0);
        $insuranceTime = $this->getParameter('insurance_time', 0);
        $insuranceCompany = $this->getParameter('insurance_company', '');
        $vehicleOwner = $this->getParameter('vehicle_owner', '');
        $contactName = $this->getParameter('contact_name');
        $phone = $this->getParameter('phone');
        $customerSource = $this->getParameter('customer_source');
        $introducer = $this->getParameter('introducer', '');
        $introducerPhone = $this->getParameter('introducer_phone', '');
        $currentKm = $this->getParameter('current_km');
        $arriveTime = $this->getParameter('arrive_time');
        $planEndTime = $this->getParameter('plan_endtime');
        $serviceConsultant = $this->getIntegerParameter('service_consultant');
        $consultantPhone = $this->getParameter('consultant_phone');
        $needWash = $this->getIntegerParameter('need_wash', 0);
        $needMaintenance = $this->getIntegerParameter('need_maintenance', 0);
        $maintenanceKm = $this->getParameter('maintenance_km', 0);
        $maintenanceCycle = $this->getParameter('maintenance_cycle', 0);
        $nextMaintenanceKm = $this->getParameter('next_maintenance_km', 0);
        $nextMaintenanceTime = $this->getParameter('next_maintenance_time', 0);
        $faultIntro = $this->getParameter('fault_intro', '');
        $checkImages = $this->getParameter('check_images', '');

        $values = [
            'vehicle_number' => $vehicleNumber,
            'vehicle_vin' => $vehicleVin,
            'brand_id' => $brandId,
            'brand_model' => $model,
            'vehicle_model' => $vehicleModel,
            'vehicle_color' => $vehicleColor,
            'model_class' => $modelClass,
            'buy_time' => $buyTime,
            'insurance_time' => $insuranceTime,
            'insurance_company' => $insuranceCompany,
            'vehicle_owner' => $vehicleOwner,
            'contact_name' => $contactName,
            'phone' => $phone,
            'customer_source' => $customerSource,
            'introducer' => $introducer,
            'introducer_phone' => $introducerPhone,
            'current_km' => $currentKm,
            'arrive_time' => $arriveTime,
            'plan_endtime' => $planEndTime,
            'service_consultant' => $serviceConsultant,
            'consultant_phone' => $consultantPhone,
            'need_wash' => $needWash,
            'need_maintenance' => $needMaintenance,
            'maintenance_km' => $maintenanceKm,
            'maintenance_cycle' => $maintenanceCycle,
            'next_maintenance_km' => $nextMaintenanceKm,
            'next_maintenance_time' => $nextMaintenanceTime,
            'fault_intro' => $faultIntro,
            'check_images' => $checkImages,
            'update_time' => time()
        ];

        $sn = '';
        $waitNumber = 0;
        if ($id) {
            $ret = $this->validOrderId($id);
            $sn = $ret->order_sn;
            $waitNumber = $ret->wait_number;
            Orders::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            $sn = $values['order_sn'] = date('YmdHis') . rand(1000, 9999);
            $values['order_status'] = 1;
            $values['create_time'] = time();
            $waitNumber = $values['wait_number'] = Orders::getInstance()->countListByConditions([
                    'create_time>=' => strtotime(date('Y-m-d'))
                ]) + 1;
            $id = Orders::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson([
            'order_id' => $id,
            'order_sn' => $sn,
            'wait_number' => $waitNumber
        ]);
    }

    public function saveItemAction()
    {
        $orderId = $this->getParameter('order_id');
        $isTemporary = $this->getParameter('is_temporary', 0);
        $opNumber = $this->getParameter('op_number', '-');
        $itemName = $this->getParameter('item_name');
        $opType = $this->getIntegerParameter('op_type');
        $itemTimes = $this->getParameter('item_times');
        $itemCost = $this->getParameter('item_cost');
        $discount = $this->getParameter('discount');

        $item = Items::getInstance()->getOneByField('op_number', $opNumber);
        if (!$isTemporary && !$item) {
            static::errorThrow('请输入正确的业务编号');
        }

        if ($isTemporary) {
            $opNumber = '-';
        }

        if (!$opType || !isset(BusinessConstants::OP_ITEMS_v2[$opType])) {
            static::errorThrow('请输入业务类型');
        }

        if (!$isTemporary && OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId, 'op_number' => $opNumber])) {
            static::errorThrow('已存在相同的项目，请确认是否重复添加!');
        }

        $orderInfo = $this->validOrderId($orderId);

        $values = [
            'order_id' => $orderId,
            'is_temporary' => $isTemporary ? 1 : 0,
            'op_number' => $opNumber,
            'item_name' => $itemName,
            'op_type' => $opType,
            'item_times' => $itemTimes,
            'item_cost' => $itemCost,
            'discount' => $discount,
            'final_price' => $itemTimes * $itemCost * $discount,
            'dateline' => time()
        ];
        if (!OrdersItems::getInstance()->insertByFields($values)) {
            static::errorThrow('创建项目数据失败');
        }

        OrderService::getInstance()->refreshTotalCost($orderId);

        parent::renderSuccessJson();
    }

    public function savePartsAction()
    {
        $id = $this->getIntegerParameter('id', 0);
        $orderId = $this->getParameter('order_id');
        $isTemporary = $this->getParameter('is_temporary', 0);
        $partsSn = $this->getParameter('parts_sn', '-');
        $partsName = $this->getParameter('parts_name');
        $spec = $this->getParameter('spec');
        $unit = $this->getParameter('unit');
        $sellPrice = $this->getParameter('sell_price', 0);
        $amount = $this->getIntegerParameter('amount', 1);
        $discount = $this->getParameter('discount');
        $isAdd = $this->getIntegerParameter('is_add', 0);
        $opType = $this->getIntegerParameter('op_type', 0);

        $ret = Parts::getInstance()->getOneByFields(['sn' => $partsSn, 'enable' => 1]);
        if (!$isTemporary && !$ret) {
            static::errorThrow('请输入正确的业务编号');
        }

        if ($isTemporary) {
            $partsSn = '-';
        }

        if (!isset(BusinessConstants::OP_ITEMS_v2[$opType])) {
            static::errorThrow('业务类型不存在');
        }

        $orderInfo = $this->validOrderId($orderId);

        if (!in_array($orderInfo->order_status, [1, 2, 3, 4, 5, 94, 95, 96])) {
            static::errorThrow('正常流程订单方可增加物料');
        }

        if ($ret && ($amount > $ret->amount)) {
            static::errorThrow('库存不足，当前材料配件仅剩下:' . $ret->amount . '件!');
        }

        $orderParts = OrdersParts::getInstance()->getOneByFields([
            'order_id' => $orderId,
            'parts_sn' => $partsSn,
            'parts_status' => [0, 1],
            'enable' => 1
        ]);

        if (!$isTemporary && ($orderParts && ($orderParts->id != $id))) {
            static::errorThrow('已添加相同');
        }

        $values = [
            'order_id' => $orderId,
            'is_temporary' => $isTemporary ? 1 : 0,
            'is_add' => $isAdd,
            'parts_sn' => $partsSn,
            'parts_name' => $partsName,
            'amount' => $amount,
            'spec' => $spec,
            'unit' => $unit,
            'sell_price' => $sellPrice,
            'discount' => $discount,
            'purchase_price_tax' => $ret ? $ret->purchase_price_tax : 0,
            'purchase_price_notax' => $ret ? $ret->purchase_price_notax : 0,
            'op_type' => $opType,
            'final_price' => $amount * $sellPrice * $discount,
            'dateline' => time()
        ];

        if ($id) {
            if (!OrdersParts::getInstance()->updateByFields($values, ['id' => $id])) {
                static::errorThrow('修改物料数据失败');
            }
        } else {
            if (!OrdersParts::getInstance()->insertByFields($values)) {
                static::errorThrow('创建物料数据失败');
            }
        }

        //判断订单是否没有维修项目，那么就直接到仓库
        if (!$isAdd && !OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId])) {
            //直接设置成已开工
            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $orderId]);
        }

        if ($isAdd) {
            // 增料需要初始化订单状态
            Orders::getInstance()->updateByFields(['order_status' => 96], ['id' => $orderId]);
        }

        //判断如果是修改材料数量则需要走到返工
        if (!$isAdd && $id && ($orderParts->amount != $amount) && in_array($orderInfo->order_status, [2, 3, 4])) {
            Orders::getInstance()->updateByFields(['order_status' => 96], ['id' => $orderId]);
        }

        OrderService::getInstance()->refreshTotalCost($orderId);

        parent::renderSuccessJson();
    }

    public function batchDiscountPartAction()
    {
        $batch = $this->getParameter('batch');
        BaseModel::getInstance()->beginTransaction();
        foreach ($batch as $v) {
            OrdersParts::getInstance()->updateByFields([
                'discount' => $v['discount'],
                'final_price' => $v['final_price']
            ], ['id' => $v['id']]);
        }
        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function batchDiscountItemAction()
    {
        $batch = $this->getParameter('batch');

        BaseModel::getInstance()->beginTransaction();
        foreach ($batch as $v) {
            OrdersItems::getInstance()->updateByFields([
                'discount' => $v['discount'],
                'final_price' => $v['final_price']
            ], ['id' => $v['id']]);
        }
        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function saveOtherInfoAction()
    {
        $orderId = $this->getIntegerParameter('order_id');
        $otherCost = $this->getFloatParameter('other_cost', 0);
        $nextAdvice = $this->getParameter('next_advice', '');

        $orderInfo = $this->validOrderId($orderId);

        $values = [
            'other_cost' => $otherCost,
            'next_advice' => $nextAdvice,
            'update_time' => time()
        ];

        if (in_array($orderInfo->order_status, [1, 96])) {
            $values['order_status'] = 2;
        }

        Orders::getInstance()->updateByFields($values, ['id' => $orderId]);

        OrderService::getInstance()->refreshTotalCost($orderId);

        //判断订单是否没有维修项目，那么就直接到仓库
        if (($orderInfo->order_status != 94) && !OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId])) {
            //直接设置成已开工
            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $orderId]);
        }

        if (($orderInfo->order_status == 94) &&
            !OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId, 'quality_status' => [0, 2]]) &&
            !OrdersParts::getInstance()->countListByConditions([
                'order_id' => $orderId,
                'quality_status' => 2
            ])) {
            //直接设置成已开工
            Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $orderId]);
        }

        //有新增维修项目则走开单下一步
        if (OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId, 'quality_status' => 0])
        ) {
            Orders::getInstance()->updateByFields(['order_status' => 2], ['id' => $orderId]);
        }


        //如果有待质检的项目，且没有待质检的材料，那么就返回到班组
        if (OrdersItems::getInstance()->countListByConditions(['order_id' => $orderId, 'quality_status' => 2])
            && !OrdersParts::getInstance()->countListByConditions(['order_id' => $orderId, 'quality_status' => 2])) {
            Orders::getInstance()->updateByFields(['order_status' => 95], ['id' => $orderId]);
        }


        //已退款订单重新开始订单
        Orders::getInstance()->updateByFields(['pay_status' => 0], ['id' => $orderId, 'pay_status' => 3]);

        parent::renderSuccessJson();
    }

    public function getPartListAction()
    {
        $orderId = $this->getParameter('order_id');

        $list = OrdersParts::getInstance()->getListByConditions(['order_id' => $orderId], 0,
            BusinessConstants::MAX_RETURN_SIZE, 'id', 'ASC');

        foreach ($list as &$v) {
            $v->type_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            switch ($v->parts_status) {
                case 0:
                    $v->parts_status = '待发货';
                    break;
                case 1:
                    $v->parts_status = '已发货';
                    break;
                case 2:
                    $v->parts_status = '已退货';
                    break;
            }
        }
        unset($v);
        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function getItemListAction()
    {
        $orderId = $this->getParameter('order_id');

        $list = OrdersItems::getInstance()->getListByConditions(['order_id' => $orderId], 0,
            BusinessConstants::MAX_RETURN_SIZE, 'id', 'ASC');
        foreach ($list as &$v) {
            $v->type_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            $v->total_price = $v->item_times * $v->item_cost;
        }
        unset($v);
        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function deleteItemAction()
    {
        $id = $this->getParameter('id');
        $ret = OrdersItems::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        $order = Orders::getInstance()->getOneByField('id', $ret->order_id);

        if ($order->order_status >= 5 && ($order->order_status != 95)) {
            static::errorThrow('已质检禁止删除');
        }

        BaseModel::getInstance()->beginTransaction();

        OrdersItems::getInstance()->deleteByFields(['id' => $id]);

        Commission::getInstance()->deleteByFields([
            'order_id' => $ret->order_id,
            'item_id' => $id,
            'op_type' => $ret->op_type
        ]);

        OrderService::getInstance()->refreshTotalCost($ret->order_id);

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    public function deletePartAction()
    {
        $id = $this->getParameter('id');
        $ret = OrdersParts::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        BaseModel::getInstance()->beginTransaction();

        $order = Orders::getInstance()->getOneByField('id', $ret->order_id);

        if ($order->order_status >= 5 && ($order->order_status != 94)) {
            static::errorThrow('已质检禁止删除');
        }

        // 判断材料是否已经发货，如果发货就走退货流程
        if ($ret->parts_status == 1) {
            Parts::getInstance()->addAmountBySn($ret->parts_sn, $ret->amount);

            $orderInfo = Orders::getInstance()->getOneByField('id', $ret->order_id);
            $partsInfo = Parts::getInstance()->getOneByField('sn', $ret->parts_sn);
            //记录材料变动
            PartsLog::getInstance()->insert([
                'parts_id' => $partsInfo->id,
                'order_sn' => $orderInfo->order_sn,
                'vehicle_number' => $orderInfo->vehicle_number,
                'is_out' => 0,
                'purchase_price_tax' => $ret->purchase_price_tax,
                'purchase_price_notax' => $ret->purchase_price_notax,
                'sell_price' => $ret->sell_price,
                'origin_amount' => $partsInfo->amount,
                'amount' => $ret->amount,
                'operator_user' => $this->accountName,
                'dateline' => time()
            ]);

        }

        OrdersParts::getInstance()->deleteByFields(['id' => $id]);

        OrderService::getInstance()->refreshTotalCost($ret->order_id);

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }


    public function cancelOrderAction()
    {
        $orderId = $this->getParameter('order_id');

        $order = Orders::getInstance()->getOneByField('id', $orderId);

        if (in_array($order->order_status, [5, 6, 7])) {
            static::errorThrow('已完工订单禁止作废');
        }

        Orders::getInstance()->updateByFields(['order_status' => 0, 'update_time' => time()], ['id' => $orderId]);

        parent::renderSuccessJson();
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

        parent::renderSuccessJson([
            'info' => $order,
            'service_items' => Orders::getInstance()->getOpTypeNameByOrderSn($order->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId)
        ]);
    }

    public function getOpItemsTypeAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $order = Orders::getInstance()->getOneByField('id', $orderId);

        if (!$order) {
            static::errorThrow('数据不存在');
        }

        parent::renderSuccessJson([
            'items' => Orders::getInstance()->getOpTypeNameByOrderSn($order->order_sn)
        ]);
    }

    public function getAddPartsListAction()
    {
        $orderId = $this->getParameter('order_id');

        $list = OrdersParts::getInstance()->getListByConditions([
            'order_id' => $orderId,
            'is_add' => 1,
            'enable' => 1
        ], 0, BusinessConstants::MAX_RETURN_SIZE);

        foreach ($list as &$v) {
            $v->parts_status = BusinessConstants::ORDER_PARTS_STATUS[$v->parts_status];
        }
        unset($v);

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function searchByVehicleNumberAction()
    {
        $vn = $this->getParameter('vn');

        $ret = Orders::getInstance()->getListByConditions(
            [
                '%vehicle_number%' => $vn,
                '%vehicle_vin%' => $vn,
                '%contact_name%' => $vn
            ],
            0,
            1, 'id', 'DESC', ['*'], true
        )->first();

        if ($ret) {
            $ret->consultant = Accounts::getInstance()->getUserNameByUid($ret->service_consultant);
        }

        parent::renderSuccessJson(
            [
                'info' => $ret
            ]
        );
    }
}