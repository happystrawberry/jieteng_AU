<?php
/**
 *
 *
 * @since   2018/11/2 11:42
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\ConfigBrand;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersParts;
use App\Models\OrdersPay;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class StatController extends ApiBaseController
{
    public function shipmentAction()
    {
        $opType = $this->getParameter('op_type', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));

        $res = OrdersParts::getInstance()->getStat($opType, $beginTime, $endTime);
        $counts = [];
        foreach ($res as $v) {
            $counts[] = [
                'type' => isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '',
                'num' => $v->num
            ];
        }

        parent::renderSuccessJson([
            'counts' => $counts
        ]);
    }

    public function shipmentListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $opType = $this->getParameter('op_type', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));
        $export = $this->getParameter('export', 0);

        $opType = $opType ? $opType : implode(',', array_keys(BusinessConstants::OP_ITEMS));
        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $total = OrdersParts::getInstance()->countListByStat($opType, $beginTime, $endTime);


        $list = OrdersParts::getInstance()->getListByStat($opType, $beginTime, $endTime, $pageOffset, $pageSize);
        foreach ($list as &$v) {
            $v->order_sn = $v->order_sn;
            $v->out_time = date('Y-m-d H:i:s', $v->update_time);
        }
        unset($v);

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('出货量统计');
            $fields = [
                'order_sn' => '订单号',
                'amount' => '出货数量',
                'out_time' => '出货时间'
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
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v->$f, DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="出货量统计_' . date('Y-m-d', $beginTime) . '_' . date('Y-m-d', $endTime) . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }


    public function orderAction()
    {
        $brandId = $this->getParameter('op_type', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));

        $res = Orders::getInstance()->getBrandStat($brandId, $beginTime, $endTime);
        $brandNames = ConfigBrand::getInstance()->getAllBrandNames();
        $counts = [];
        foreach ($res as $v) {
            $counts[] = [
                'brand' => isset($brandNames[$v->brand_id]) ? $brandNames[$v->brand_id] : '',
                'num' => $v->num
            ];
        }

        parent::renderSuccessJson([
            'counts' => $counts
        ]);
    }

    public function orderListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $brandId = $this->getParameter('op_type', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));
        $export = $this->getParameter('export', 0);
        $conditions = [
            'pay_time>=' => $beginTime,
            'pay_time<' => $endTime,
            'pay_status' => [1, 2]
        ];

        if ($brandId) {
            $conditions['brand_id'] = explode(',', $brandId);
        }

        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $total = Orders::getInstance()->countListByConditions($conditions);
        $list = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
        $brands = ConfigBrand::getInstance()->getAllBrandNames();

        foreach ($list as &$v) {
            $v->brand = isset($brands[$v->brand_id]) ? $brands[$v->brand_id] : '';
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('进厂量统计');
            $fields = [
                'order_sn' => '订单号',
                'brand' => '品牌',
                'brand_model' => '车型'
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
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v->$f, DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="进厂量统计_' . date('Y-m-d', $beginTime) . '_' . date('Y-m-d', $endTime) . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function sellAction()
    {
        $operator = $this->getParameter('operator', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));

        $conditions = [
            'pay_status' => [1, 2],
            'pay_time>=' => $beginTime,
            'pay_time<' => $endTime,
        ];

        if ($operator) {
            $conditions['service_consultant'] = explode(',', $operator);
        }

        $totalIncome = 0;
        $list = Orders::getInstance()->getListByConditions($conditions, 0, BusinessConstants::MAX_RETURN_SIZE);
        $orderIds = [];
        foreach ($list as $v) {
            $totalIncome += $v->total_cost;
            $orderIds[] = $v->id;
        }

        $itemList = OrdersItems::getInstance()->getListByConditions([
            'order_id' => $orderIds,
            'enable' => 1,
        ], 0, BusinessConstants::MAX_RETURN_SIZE);
        $partList = OrdersParts::getInstance()->getListByConditions([
            'order_id' => $orderIds,
            'enable' => 1,
            'parts_status' => 1
        ], 0, BusinessConstants::MAX_RETURN_SIZE);

        $num = count($orderIds);
        $tmp = [];
        $tmp[0] = 0;
        foreach (BusinessConstants::OP_ITEMS as $k => $v) {
            $tmp[$k] = 0;
        }

        foreach ($itemList as $v) {
            if ($v->op_type == 1) {
                $v->op_type = 2;
            }
            if (!$v->op_type) {
                $v->op_type = 6;
            }
            $tmp[$v->op_type] += $v->final_price;
        }
        foreach ($partList as $v) {
            if ($v->op_type == 1) {
                $v->op_type = 2;
            }
            if (!$v->op_type) {
                $v->op_type = 6;
            }
            $tmp[$v->op_type] += $v->final_price;
        }


        $data = [];
        foreach (BusinessConstants::OP_ITEMS_v2 as $k => $v) {
            $data[] = [
                'total' => $tmp[$k],
                'type' => $v
            ];
        }

        parent::renderSuccessJson([
            'total_income' => $totalIncome,
            'total_commission' => 0,
            'single_value' => $num ? round($totalIncome / $num, 2) : 0,
            'vehicle_num' => $num,
            'list' => $data
        ]);
    }

    public function sellListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $operator = $this->getParameter('operator', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));
        $export = $this->getParameter('export', 0);

        $conditions = [
            'pay_time>=' => $beginTime,
            'pay_time<' => $endTime,
            'pay_status' => [1, 2]
        ];

        if ($operator) {
            $conditions['service_consultant'] = explode(',', $operator);
        }

        $total = Orders::getInstance()->countListByConditions($conditions);

        $list = [];

        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        if ($total) {
            $list = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
            foreach ($list as &$v) {
                $v->bill_time = date('Y-m-d H:i:s', $v->pay_time);
                $v->consultant_name = Accounts::getInstance()->getUserNameByUid($v->service_consultant);
            }
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('销售额统计');
            $fields = [
                'order_sn' => '订单号',
                'consultant_name' => '服务顾问',
                'bill_time' => '结算时间',
                'total_cost' => '结算金额'
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
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v->$f, DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="总销售额统计_' . date('Y-m-d', $beginTime) . '_' . date('Y-m-d', $endTime) . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function payWayAction()
    {
        //修复并同步老的数据到新表
        Orders::getInstance()->fixPayWayData();

        $payWay = $this->getParameter('pay_way', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m')));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));

        $list = OrdersPay::getInstance()->groupByPayWay($beginTime, $endTime, $payWay);


        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function payWayListAction()
    {
        //修复并同步老的数据到新表
        Orders::getInstance()->fixPayWayData();
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $payWay = $this->getParameter('pay_way', '');
        $beginTime = $this->getIntegerParameter('begin_time', strtotime(date('Y-m', strtotime('-1 months'))));
        $endTime = $this->getIntegerParameter('end_time', strtotime(date('Y-m', strtotime('+1 months'))));
        $export = $this->getIntegerParameter('export', 0);

        $conds = ['dateline>=' => $beginTime, 'dateline<=' => $endTime];

        if ($payWay) {
            $conds['pay_way'] = explode(',', $payWay);
        }


        if ($export) {
            $pageOffset = 0;
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $total = OrdersPay::getInstance()->countListByTimeAndPayWay($beginTime, $endTime, $payWay);
        $list = [];
        if ($total) {
            $list = OrdersPay::getInstance()->getListByTimeAndPayWay($beginTime, $endTime, $payWay);
            $list = array_slice($list, $pageOffset, $pageSize);
            foreach ($list as &$v) {
                $v['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
            }
            unset($v);
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('支付方式统计');
            $fields = [
                'order_sn' => '订单号',
                'pay_time' => '支付时间',
            ];
            foreach (BusinessConstants::PAY_STATUS_SET as $k => $n) {
                $fields['p_' . $k] = $n;
            }

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->getColumnDimension(chr(ord('A') + $k))->setWidth(20);
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }
            // 初始化下拉选项
            foreach ($list as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    if ((strpos($f, 'p_') !== false) && ($w = str_replace('p_', '', $f))) {

                        $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v['way_cost'][$w], DataType::TYPE_STRING);
                    } else {
                        $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v[$f], DataType::TYPE_STRING);
                    }
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="支付方式统计_' . date('Y-m-d', $beginTime) . '_' . date('Y-m-d', $endTime) . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }
}