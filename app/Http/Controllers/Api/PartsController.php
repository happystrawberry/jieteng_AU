<?php
/**
 *
 *
 * @since   2018/6/4 22:04
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\BaseModel;
use App\Models\ConfigBrand;
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

class PartsController extends ApiBaseController
{

    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $pickStatus = $this->getIntegerParameter('order_status', -1);
        $conditions = [];
        $conditions['order_status'] = [2, 3, 4, 94, 95];

        if ($pickStatus >= 0) {
            $conditions['pick_status'] = $pickStatus;
        } else {
            $conditions['pick_status'] = [0, 1];
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
                    'plan_endtime' => $v->plan_endtime,
                    'order_status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'vehicle_vin' => $v->vehicle_vin,
                    'vehicle_number' => $v->vehicle_number,
                    'service_items' => Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn),
                    'arrive_time' => $v->arrive_time,
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCountByPick()
        ]);
    }

    /**
     * 库存列表
     *
     */
    public function supplyListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $partsName = $this->getParameter('parts_name', '');
        $sn = $this->getParameter('sn', '');
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        $export = $this->getParameter('export', 0);
        $conditions = [];
        if ($partsName) {
            $conditions['%parts_name%'] = $partsName;
        }
        if ($sn) {
            $conditions['%sn%'] = $sn;
        }
        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
        }

        if ($export) {
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
            $pageOffset = 0;
        }

        $list = [];
        $total = Parts::getInstance()->countListByConditions($conditions);
        if ($total) {
            $list = Parts::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
            foreach ($list as &$v) {
                $v->service_name = $v->op_type ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            }
            unset($v);
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('库存表');
            $fields = [
                'sn' => '材料编码',
                'parts_name' => '材料名称',
                'target_model' => '对应车型',
                'spec' => '规格',
                'unit' => '单位',
                'amount' => '库存量',
                'purchase_price_tax' => '进货单价（含税）',
                'purchase_price_notax' => '进货单价（不含税）',
                'sell_price' => '出货单价',
                'parts_location' => '库存位置',
                'notes' => '备注'
            ];

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }

            foreach ($list as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v->$f, DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="库存数据' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
        ]);
    }

    public function viewOrderAction()
    {
        $orderId = $this->getParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status>' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单数据不存在');
        }

        //获取材料清单
        $list = OrdersParts::getInstance()->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);
        $partsSns = [];
        foreach ($list as $v) {
            $partsSns[] = $v->parts_sn;
        }
        $parts = Parts::getInstance()->getPartsBySns($partsSns);


        foreach ($list as &$v) {
            $v->type_name = $v->op_type ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            switch ($v->parts_status) {
                case 0:
                    $v->status = '待发货';
                    break;
                case 1:
                    $v->status = '已发货';
                    break;
                case 2:
                    $v->status = '已退货';
                    break;
            }
            $v->target_model = isset($parts[$v->parts_sn]) ? $parts[$v->parts_sn]->target_model : '';
        }
        unset($v);

        parent::renderSuccessJson([
            'list' => $list,
            'order_status' => BusinessConstants::ORDER_STATUS[$orderInfo->order_status],
            'vehicle_number' => $orderInfo->vehicle_number,
            'wait_number' => $orderInfo->wait_number,
            'op_type' => array_map(function ($i) {
                return BusinessConstants::OP_ITEMS[$i];
            }, Orders::getInstance()->getOpTypeList($orderId)),
            'order_sn' => $orderInfo->order_sn,
            'vehicle_vin' => $orderInfo->vehicle_vin,
            'vehicle_color' => $orderInfo->vehicle_color,
            'vehicle_model' => $orderInfo->vehicle_model,
            'barcode' => UtilFunction::getBarcode($orderInfo->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId),
            'brand' => ConfigBrand::getInstance()->getOneByField('id', $orderInfo->brand_id)->brand_name,
            'model' => $orderInfo->brand_model
        ]);
    }

    /**
     *  设置发货
     *
     */
    public function sendOutPartsAction()
    {
        $id = $this->getIntegerParameter('id');

        // 如果已经退货不能再发货
        $ret = OrdersParts::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        if ($ret->parts_status == 1) {
            static::errorThrow('已发货商品请勿重复发货');
        }

        if ($ret->parts_status == 2) {
            static::errorThrow('已退货商品无法再发货');
        }

        BaseModel::getInstance()->beginTransaction();

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $ret->order_id, 'order_status>' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单不存在');
        }

        // 只有已经调度才能设置发货
        if (!in_array($orderInfo->order_status, [3, 4, 94, 95, 96])) {
            static::errorThrow('只有已经被调度且未完工的订单才能发货');
        }

        $partsInfo = Parts::getInstance()->getOneByFields(['sn' => $ret->parts_sn, 'enable' => 1]);

        if (!$ret->is_temporary && !$partsInfo) {
            static::errorThrow('配件不存在');
        }


        if ($ret->is_temporary) {

        } else {
            if (!Parts::getInstance()->reduceAmount($partsInfo->id, $ret->amount)) {
                static::errorThrow('库存不足无法发货');
            }

            PartsLog::getInstance()->insertByFields(
                [
                    'parts_id' => $partsInfo->id,
                    'order_sn' => $orderInfo->order_sn,
                    'vehicle_number' => $orderInfo->vehicle_number,
                    'is_out' => 1,
                    'purchase_price_tax' => $ret->purchase_price_tax,
                    'purchase_price_notax' => $ret->purchase_price_notax,
                    'sell_price' => $ret->sell_price,
                    'origin_amount' => $partsInfo->amount,
                    'amount' => $ret->amount,
                    'operator_user' => $this->accountName,
                    'dateline' => time()
                ]
            );
        }

        if (!OrdersParts::getInstance()->updateByFields(
            ['parts_status' => 1, 'update_time' => time(), 'update_user' => $this->accountName],
            ['id' => $id]
        )) {
            static::errorThrow('设置发送状态失败');
        }

        //检查是否材料都已经操作
        if (!OrdersParts::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'parts_status' => 0])) {
            //全部都操作了说明已发货完毕
            Orders::getInstance()->updateByFields(['pick_status' => 1], ['id' => $ret->order_id, 'order_status<>' => 0]);

            //判断订单是否没有维修项目，那么就直接到仓库
            if (!OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id])) {
                //直接设置成已完工
                Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $ret->order_id]);
            }
        }

        OrderService::getInstance()->refreshTotalCost($ret->order_id);

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }

    /**
     *  设置退货
     *
     * @throws \Exception
     */
    public function returnPartsAction()
    {
        $id = $this->getIntegerParameter('id');
        $num = $this->getIntegerParameter('num');
        $ret = OrdersParts::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        if ($ret->parts_status != 1) {
            static::errorThrow('只有已发货订单才能退货');
        }

        if ($num != $ret->amount) {
            static::errorThrow('退货数量错误');
        }

        BaseModel::getInstance()->beginTransaction();

        if (!OrdersParts::getInstance()->updateByFields([
            'parts_status' => 2,
            'update_time' => time(),
            'update_user' => $this->accountName
        ], ['id' => $id])) {
            static::errorThrow('修改库存失败');
        }

        //检查是否材料都已经操作
        if (!OrdersParts::getInstance()->countListByConditions(['order_id' => $ret->order_id, 'parts_status' => 0])) {
            //全部都操作了说明已发货完毕
            Orders::getInstance()->updateByFields(['pick_status' => 1], ['id' => $ret->order_id, 'order_status<>' => 0]);

            //判断订单是否没有维修项目，那么就直接到仓库
            if (!OrdersItems::getInstance()->countListByConditions(['order_id' => $ret->order_id])) {
                //直接设置成已完工
                Orders::getInstance()->updateByFields(['order_status' => 4], ['id' => $ret->order_id]);
            }
        }

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

        // 已经发货商品退货
        Parts::getInstance()->addAmountBySn($ret->parts_sn, $num);

        OrderService::getInstance()->refreshTotalCost($ret->order_id);

        BaseModel::getInstance()->commitTransaction();

        parent::renderSuccessJson();
    }


    public function viewPartsAction()
    {
        $id = $this->getIntegerParameter('id');

        $parts = Parts::getInstance()->getOneByField('id', $id);

        if (!$parts) {
            static::errorThrow('配件数据不存在');
        }

        parent::renderSuccessJson([
            'info' => $parts
        ]);
    }

    public function savePartsAction()
    {
        $id = $this->getParameter('id', 0);
        $sn = $this->getParameter('sn');
        $opType = $this->getParameter('op_type', 0);
        $partsName = $this->getParameter('parts_name');
        $targetModel = $this->getParameter('target_model', '');
        $spec = $this->getParameter('spec');
        $unit = $this->getParameter('unit');
        $amount = $this->getIntegerParameter('amount');
        $purchasePriceTax = $this->getParameter('purchase_price_tax');
        $purchasePriceNoTax = $this->getParameter('purchase_price_notax');
        $sellPrice = $this->getParameter('sell_price');
        $partsLocation = $this->getParameter('parts_location', '');

        $values = [
            'op_type' => $opType,
            'sn' => $sn,
            'parts_name' => $partsName,
            'target_model' => $targetModel,
            'spec' => $spec,
            'unit' => $unit,
            'amount' => $amount,
            'purchase_price_tax' => $purchasePriceTax,
            'purchase_price_notax' => $purchasePriceNoTax,
            'sell_price' => $sellPrice,
            'parts_location' => $partsLocation,
            'update_time' => time()
        ];

        $ret = Parts::getInstance()->getOneByFields(['sn' => $sn, 'enable' => 1]);

        if ($ret && (!$id || $ret->id != $id)) {
            static::errorThrow('已存在相同材料编号');
        }

        if ($id) {
            $ret = Parts::getInstance()->getOneByField('id', $id);

            if (!$ret) {
                static::errorThrow('id数值不合法');
            }

            if ($amount > $ret->amount || $amount < $ret->amount) {
                //入库 or 出库
                PartsLog::getInstance()->insertByFields([
                    'parts_id' => $id,
                    'is_out' => ($amount > $ret->amount) ? 0 : 1,
                    'purchase_price_tax' => $purchasePriceTax,
                    'purchase_price_notax' => $purchasePriceNoTax,
                    'sell_price' => $sellPrice,
                    'origin_amount' => $ret->amount,
                    'amount' => ($amount > $ret->amount) ? ($amount - $ret->amount) : ($ret->amount - $amount),
                    'operator_user' => $this->accountName,
                    'dateline' => time()
                ]);
            }

            Parts::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            $values['create_time'] = time();
            Parts::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();

    }

    public function saveNotesAction()
    {
        $id = $this->getParameter('id');
        $notes = $this->getParameter('notes');
        if (!Parts::getInstance()->updateByFields(['notes' => $notes], ['id' => $id])) {
            static::errorThrow('保存备注失败');
        }

        parent::renderSuccessJson();
    }


    public function deleteAction()
    {
        $id = $this->getIntegerParameter('id');
        $enable = $this->getIntegerParameter('enable', 0);

        if (!Parts::getInstance()->updateByFields(['enable' => $enable ? 1 : 0], ['id' => $id])) {
            static::errorThrow('操作失败');
        }

        parent::renderSuccessJson();
    }


    public function searchPartsListAction()
    {
        $opType = $this->getIntegerParameter('op_type', 0);
        $query = $this->getParameter('query');

        parent::renderSuccessJson([
            'list' => Parts::getInstance()->searchParts($opType, $query)
        ]);
    }

    public function getPartsLogListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $partsName = $this->getParameter('parts_name', '');
        $partsId = $this->getIntegerParameter('parts_id', 0);
        $beginTime = $this->getIntegerParameter('begin_time', 0);
        $endTime = $this->getIntegerParameter('end_time', 0);
        $isOut = $this->getIntegerParameter('is_out', -1);
        $export = $this->getParameter('export', 0);

        $conditions = [];

        if ($isOut >= 0) {
            $conditions['is_out'] = $isOut;
        }

        if ($partsName) {
            $conditions['parts_id'] = Parts::getInstance()->getPartsIdByName($partsName);
        }

        if ($partsId) {
            $conditions['parts_id'] = $partsId;
        }

        if ($beginTime) {
            $conditions['dateline>='] = $beginTime;
        }

        if ($endTime) {
            $conditions['dateline<='] = $endTime;
        }

        if ($export) {
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $total = PartsLog::getInstance()->countListByConditions($conditions);
        $list = [];
        if ($total) {
            $list = PartsLog::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize, 'id', 'desc');

            $partsId = [];
            foreach ($list as $v) {
                $partsId[] = $v->parts_id;
            }

            $partsInfo = Parts::getInstance()->getPartsByIds($partsId);

            foreach ($list as &$v) {

                $order = null;
                if (($v->order_sn != '-') && $v->order_sn) {
                    $order = Orders::getInstance()->getOneByField('order_sn', $v->order_sn);
                }

                $v->parts_sn = $partsInfo[$v->parts_id]->sn;
                $v->target_model = $partsInfo[$v->parts_id]->target_model;
                $v->parts_name = $partsInfo[$v->parts_id]->parts_name;
                $v->spec = $partsInfo[$v->parts_id]->spec;
                $v->unit = $partsInfo[$v->parts_id]->unit;
                $v->status = $v->is_out ? '出库' : '入库';
                $v->order_id = $order ? $order->id : 0;
            }
            unset($v);
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('出入库记录表');
            $fields = [
                'order_sn' => '订单编号',
                'vehicle_number' => '车牌',
                'parts_sn' => '材料编码',
                'parts_name' => '材料名称',
                'target_model' => '对应车型',
                'spec' => '规格',
                'unit' => '单位',
                'origin_amount' => '变动前数量',
                'amount' => '变动数量',
                'now_amount' => '变动后数量',
                'purchase_price_tax' => '进货单价（含税）',
                'purchase_price_notax' => '进货单价（不含税）',
                'sell_price' => '出货单价',
                'is_out' => '状态',
                'dateline' => '时间'
            ];

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->getColumnDimension(chr(ord('A') + $k))->setWidth(20);
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }

            foreach ($list as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    if ($f == 'dateline') {
                        $workSheet->setCellValueByColumnAndRow($i, (2 + $k), date('Y-m-d H:i:s', $v->dateline));
                    } else {
                        if ($f == 'now_amount') {
                            $workSheet->setCellValueByColumnAndRow($i, (2 + $k),
                                $v->is_out ? ($v->origin_amount - $v->amount) : ($v->origin_amount + $v->amount));
                        } else {
                            $workSheet->setCellValueByColumnAndRow($i, (2 + $k), ($i == 14) ? ($v->$f ? '出库' : '入库') : ('="' . $v->$f . '"'));
                        }
                    }
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="入库出库记录' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }


        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }


    public function importAction()
    {
        if (!$this->request->hasFile('file') || !$this->request->file('file')->isValid()) {
            static::errorThrow('请上传Excel文档');
        }
        /**
         * @var $file \Illuminate\Http\UploadedFile
         */
        $file = $this->request->file('file');


        $excel = IOFactory::load($file->path());

        $data = $excel->getSheet(0)->toArray();

        if (count($data) < 2) {
            static::errorThrow('当前Excel文件内容为空');
        }

        // 检查列数
        if (count($data[0]) !== 12) {
            static::errorThrow('Excel文档格式不正确');
        }

        // 获取数据库字段名
        $tableFields = array_slice(Parts::getInstance()->getAllFields(), 1, 12);
        $nameFields = $data[0];
        $data = array_slice($data, 1);
        $values = [];
        foreach ($data as $v) {
            $value = [];
            foreach ($v as $k => $val) {
                $val = trim($val);
                if ((strlen($val) == 0) && !in_array($k, [3, 11])) {
                    static::errorThrow($nameFields[$k] . '内容为空,请检查');
                }

                if ($k == 0) {
                    if (!in_array($val, BusinessConstants::OP_ITEMS)) {
                        static::errorThrow('业务类别选择错误');
                    }

                    $value[$tableFields[$k]] = array_search($val, BusinessConstants::OP_ITEMS);
                } else {
                    $value[$tableFields[$k]] = $val;
                }
            }

            $value['update_time'] = time();
            $value['create_time'] = time();
            $values[$v[1]] = $value;
        }

        foreach ($values as $sn => $v) {
            if ($ret = Parts::getInstance()->getOneByField('sn', $sn)) {
                $v['create_time'] = $ret->create_time;
                Parts::getInstance()->updateByFields($v, ['id' => $ret->id]);

                unset($values[$sn]);
            }
        }

        if ($values) {
            Parts::getInstance()->batchInsertByFields($values);
        }

        parent::renderSuccessJson();
    }


    public function saveOrderPartsAction()
    {
        $app = \App::make("\\App\\Http\\Controllers\\Api\\OrderController");

        return \App::call([$app, 'savePartsAction']);
    }
}