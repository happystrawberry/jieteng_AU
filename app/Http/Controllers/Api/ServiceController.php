<?php
/**
 *
 *
 * @since   2018/6/3 9:25
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\ConfigBrand;
use App\Models\FeedBack;
use App\Models\FeedbackQuestions;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersNotes;
use App\Models\OrdersParts;
use App\Models\Questions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ServiceController extends ApiBaseController
{
    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $orderStatus = $this->getParameter('order_status', '');

        $conditions = [];
        if ($orderStatus) {
            $conditions['order_status'] = explode(',', $orderStatus);
        } else {
            $conditions['order_status'] = [6, 7];
        }

        $vehicleNumber = $this->getParameter('vehicle_number', '');
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        $orderSn = $this->getParameter('order_sn', '');
        if ($orderSn) {
            $conditions['order_sn'] = $orderSn;
        }
        if ($vehicleNumber) {
            $conditions['%vehicle_number%'] = $vehicleNumber;
        }
        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
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
                    'vehicle_owner' => $v->vehicle_owner,
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'end_time' => OrdersItems::getInstance()->getQualityEndTime($v->id),
                    'phone' => $v->phone,
                    'total_cost' => $v->total_cost,
                    'order_status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'arrive_time' => $v->arrive_time,
                    'contact_name' => $v->contact_name,
                    'bill_operator' => Accounts::getInstance()->getUserNameByUid($v->bill_operator),
                    'brand' => ConfigBrand::getInstance()->getOneByField('id', $v->brand_id)->brand_name,
                    'model' => $v->brand_model,
                    'vehicle_model' => $v->vehicle_model
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
            'counts' => Orders::getInstance()->groupCount([6, 7], false, false, $beginTime, $endTime)
        ]);
    }

    public function viewOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status>' => 0]);

        if (!$orderInfo) {
            static::errorThrow('订单数据不存在');
        }

        $itemList = OrdersItems::getInstance()->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);

        foreach ($itemList as &$v) {
            $v->type_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
        }
        unset($v);
        $feedBack = FeedBack::getInstance()->getOneByField('order_id', $orderId);


        //获取回访问题列表
        $questionList = Questions::getInstance()->getAllListByFields(['enable' => 1], ['id', 'question', 'options', 'need_cause']);
        $feedBackQuestion = FeedbackQuestions::getInstance()->getChoiceByOrderId($orderId);
        foreach ($questionList as &$v) {
            $v->options = json_decode($v->options);
            $v->need_cause = json_decode($v->need_cause);
            $v->choice = [
                'pos' => isset($feedBackQuestion[$v->id]) ? $feedBackQuestion[$v->id]->choice : '-1',
                'causes' => isset($feedBackQuestion[$v->id]) ? $feedBackQuestion[$v->id]->causes : ''
            ];
        }
        unset($v);

        parent::renderSuccessJson([
            'order_id' => $orderId,
            'item_list' => $itemList,
            'part_list' => OrdersParts::getInstance()->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE),
            'order_status' => BusinessConstants::ORDER_STATUS[$orderInfo->order_status],
            'vehicle_number' => $orderInfo->vehicle_number,
            'vehicle_owner' => $orderInfo->vehicle_owner,
            'wait_number' => $orderInfo->wait_number,
            'phone' => $orderInfo->phone,
            'order_sn' => $orderInfo->order_sn,
            'vehicle_vin' => $orderInfo->vehicle_vin,
            'vehicle_color' => $orderInfo->vehicle_color,
            'vehicle_model' => $orderInfo->vehicle_model,
            'model' => $orderInfo->brand_model,
            'brand' => ConfigBrand::getInstance()->getOneByField('id', $orderInfo->brand_id)->brand_name,
            'final_endtime' => OrdersItems::getInstance()->getQualityEndTime($orderId),
            'feedback_maintenance' => $feedBack ? $feedBack->maintenance : 0,
            'feedback_service_attitude' => $feedBack ? $feedBack->service_attitude : 0,
            'feedback_service_environment' => $feedBack ? $feedBack->service_environment : 0,
            'feedback_rest_zone' => $feedBack ? $feedBack->rest_zone : 0,
            'feedback_remark' => $feedBack ? $feedBack->remark : '',
            'owner_age' => $feedBack ? $feedBack->owner_age : 0,
            'owner_sexy' => $feedBack ? $feedBack->owner_sexy : 0,
            'owner_addr' => $feedBack ? $feedBack->owner_addr : '',
            'barcode' => UtilFunction::getBarcode($orderInfo->order_sn),
            'notes' => OrdersNotes::getInstance()->getNumByOrderId($orderId),
            'contact_name' => $orderInfo->contact_name,
            'question_list' => $questionList,
            'customer_source' => $orderInfo->customer_source
        ]);
    }

    public function saveFeedBackAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $ret = Orders::getInstance()->getOneByFields(['id' => $orderId, 'order_status' => [6, 7]]);

        if (!$ret) {
            static::errorThrow('不存在可以记录回访的订单');
        }

        // 判断是否选择了问题
        if ($ques = $this->getParameter('questions', [])) {
            $list = Questions::getInstance()->getListByConditions(['enable' => 1], 0, BusinessConstants::MAX_RETURN_SIZE);

            if (count($ques) !== count($list)) {
                static::errorThrow('答案未填写完整');
            }
            FeedbackQuestions::getInstance()->deleteByFields(['order_id' => $orderId]);
            $values = [];
            foreach ($ques as $v) {
                $values[] = [
                    'order_id' => $orderId,
                    'question_id' => $v['id'],
                    'choice' => $v['choice'],
                    'causes' => $v['cause'],
                    'dateline' => time()
                ];
            }
            FeedbackQuestions::getInstance()->batchInsertByFields($values);
        }

        $values = [
            'order_id' => $orderId,
            'maintenance' => $this->getParameter('maintenance', 0),
            'service_attitude' => $this->getParameter('service_attitude', 0),
            'service_environment' => $this->getParameter('service_environment', 0),
            'rest_zone' => $this->getParameter('rest_zone', 0),
            'remark' => $this->getParameter('remark', ''),
            'owner_age' => $this->getIntegerParameter('age', 0),
            'owner_sexy' => $this->getIntegerParameter('sexy', 0),
            'owner_addr' => $this->getParameter('address', ''),
            'dateline' => time()
        ];

        if (FeedBack::getInstance()->getOneByField('order_id', $orderId)) {
            FeedBack::getInstance()->updateByFields($values, ['order_id' => $orderId]);
        } else {
            FeedBack::getInstance()->insertByFields($values);
        }

        Orders::getInstance()->updateByFields(['order_status' => 7], ['id' => $orderId, 'order_status' => [6, 7]]);

        parent::renderSuccessJson();
    }

    public function saveQuestionAction()
    {
        $id = $this->getIntegerParameter('id', 0);
        $question = $this->getParameter('question');
        $options = $this->getParameter('options');
        $cause = $this->getParameter('cause');
        $values = [
            'question' => $question,
            'options' => json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'need_cause' => json_encode($cause, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'dateline' => time()
        ];

        if ($id) {
            Questions::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            Questions::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function questionListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $query = $this->getParameter('query', '');
        $conditions = [];
        if ($query) {
            $conditions['%question%'] = $query;
        }

        $total = Questions::getInstance()->countListByConditions($conditions);

        $list = [];
        if ($total) {
            $list = Questions::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);

            $qids = [];
            foreach ($list as $v) {
                $qids[] = $v->id;
            }

            $counts = FeedbackQuestions::getInstance()->countByQids($qids);

            foreach ($list as &$v) {
                $v->nums = isset($counts[$v->id]) ? $counts[$v->id] : 0;
            }
            unset($v);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage),
        ]);
    }

    public function switchQuestionAction()
    {
        $id = $this->getIntegerParameter('id');

        $res = Questions::getInstance()->getOneByField('id', $id);

        if (!$res) {
            static::errorThrow('数据不存在!');
        }

        $enable = $res->enable ? 0 : 1;
        Questions::getInstance()->updateByFields(['enable' => $enable], ['id' => $id]);

        parent::renderSuccessJson([
            'enable' => $enable
        ]);
    }

    public function viewQuestionAction()
    {
        $id = $this->getIntegerParameter('id');
        $export = $this->getIntegerParameter('export', 0);
        $res = Questions::getInstance()->getOneByField('id', $id);
        if (!$res) {
            static::errorThrow('数据不存在');
        }
        $res->options = json_decode($res->options);
        list($data, $total) = FeedbackQuestions::getInstance()->getQuestionStatById($id);
        $arr = [];
        foreach ($data as $v) {
            $arr[] = [
                'option' => isset($res->options[$v['choice']]) ? $res->options[$v['choice']] : '',
                'num' => $v['num'],
                'percentage' => (round($v['num'] / $total, 4) * 100) . '%'
            ];
        }
        $res->stat = $arr;

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle($res->question);
            $fields = [
                'option' => '选项',
                'num' => '占比分数',
                'percentage' => '占比',
            ];

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->getColumnDimension(chr(ord('A') + $k))->setWidth(20);
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }

            // 初始化下拉选项
            foreach ($res->stat as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    $workSheet->setCellValueExplicitByColumnAndRow($i, (2 + $k), $v[$f], DataType::TYPE_STRING);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="回访问题统计' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }

        parent::renderSuccessJson([
            'info' => $res
        ]);
    }
}