<?php
/**
 *
 *
 * @since   2018/6/3 10:08
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\AccountsGroup;
use App\Models\BUser;
use App\Models\Items;
use App\Models\Orders;
use App\Models\OrdersNotes;
use App\Models\Parts;
use App\Models\WorkTaskPeople;
use App\Services\AccountService;

class CommonController extends ApiBaseController
{
    public function uploadAction()
    {
        if (!$this->request->hasFile('file') || !$this->request->file('file')->isValid()) {
            static::errorThrow('请上传文件');
        }

        $uploadPath = base_path() . '/public';
        $suffiexPath = '/uploads/';
        $file = $this->request->file('file');
        if (strpos($file->getMimeType(), 'image') === 0) {
            $suffiexPath .= 'image/' . date('Ymd') . '/';
        } else {
            $suffiexPath .= 'file/' . date('Ymd') . '/';
        }

        $fileName = $file->move($uploadPath . $suffiexPath,
            (time() . rand(10000, 99999) . '.' . $file->extension()))->getFilename();

        parent::renderSuccessJson([
            'path' => $suffiexPath . $fileName,
            'url' => $this->request->getSchemeAndHttpHost() . $suffiexPath . $fileName
        ]);
    }

    public function getOpTypeListAction()
    {
        $list = [];

        foreach (BusinessConstants::OP_ITEMS_v2 as $k => $v) {
            $list[] = [
                'id' => $k,
                'name' => $v
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function getModelClassListAction()
    {
        parent::renderSuccessJson([
            'list' => BusinessConstants::VEHICLE_MODEL_CLASS
        ]);
    }

    public function searchAccountNameAction()
    {
        $name = $this->getParameter('name');
        $list = Accounts::getInstance()->getListByConditions(
            ['%username%' => $name, 'enable' => 1, 'display' => 1],
            0, BusinessConstants::MAX_RETURN_SIZE, 'id', 'desc', ['id', 'username', 'phone', 'ding_department']
        );

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function saveOrderNoteAction()
    {
        $orderId = $this->getParameter('order_id');
        $message = $this->getParameter('message');

        if (!OrdersNotes::getInstance()->insertByFields([
            'order_id' => $orderId,
            'message' => $message,
            'account_name' => $this->accountName,
            'account_uid' => $this->uid,
            'dateline' => time()
        ])) {
            static::errorThrow('添加备注失败');
        };

        parent::renderSuccessJson();
    }

    public function orderNoteListAction()
    {
        $orderId = $this->getParameter('order_id');

        $list = OrdersNotes::getInstance()->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function searchByVehicleNumberAction()
    {
        $vn = $this->getParameter('vn', '');

        parent::renderSuccessJson([
            'list' => BUser::getInstance()->searchVehicleNumber($vn)
        ]);
    }

    public function getAllOpItemsAction()
    {
        $opType = $this->getIntegerParameter('op_type', 0);
        $class = strtolower($this->getParameter('class'));
        $times = $class . '_times';
        $cost = $class . '_cost';
        $conditions = [];
        $conditions['enable'] = 1;
        if ($opType) {
            $conditions['op_type'] = $opType;
        }

        $list = Items::getInstance()->getAllListByFields($conditions, ['id', 'item_name', $cost, $times, 'op_number']);
        $group = [];
        foreach ($list as $v) {
            $pinyin = strtoupper(substr(\Pinyin::abbr($v->item_name), 0, 1));
            $group[] = [
                'first' => $pinyin,
                'id' => $v->id,
                'name' => $v->item_name,
                'times' => $v->$times,
                'cost' => $v->$cost,
                'op_number' => $v->op_number
            ];
        }

        parent::renderSuccessJson([
            'list' => $group
        ]);
    }

    public function getAllPartsAction()
    {
        $opType = $this->getParameter('op_type', 0);

        $list = Parts::getInstance()->getAllListByFields(
            $opType ? ['op_type' => $opType, 'enable' => 1] : ['enable' => 1],
            ['id', 'sn', 'parts_name', 'target_model', 'spec', 'unit', 'sell_price', 'amount']
        );

        parent::renderSuccessJson([
            'list' => $list
        ], 'ok', JSON_UNESCAPED_UNICODE);
    }


    public function getCodeBarAction()
    {
        $sn = $this->getParameter('sn');

        return response(UtilFunction::getBarcode($sn, false))->header('Content-Type', 'image/png');
    }

    public function doPasswordAction()
    {
        $password = $this->getParameter('password');
        $salt = $this->getParameter('salt');

        echo \Hash::make($password . $salt);
    }

    public function resetOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByField('id', $orderId);

        if (!$orderInfo || $orderInfo->pay_status == 2) {
            static::errorThrow('已支付订单不能返回到开单');
        }

        Orders::getInstance()->updateByFields(['order_status' => 1], ['id' => $orderId]);
        //WorkTask::getInstance()->deleteByFields(['order_id' => $orderId]);
        // WorkTaskPeople::getInstance()->deleteByFields(['order_id' => $orderId]);
        parent::renderSuccessJson();
    }


    public function returnOrderAction()
    {
        $orderId = $this->getIntegerParameter('order_id');

        $orderInfo = Orders::getInstance()->getOneByField('id', $orderId);

        if (!$orderInfo || $orderInfo->pay_status == 2) {
            static::errorThrow('已支付订单不能返回到开单');
        }

        Orders::getInstance()->updateByFields(['order_status' => 1], ['id' => $orderId]);
        //WorkTask::getInstance()->deleteByFields(['order_id' => $orderId]);
        // WorkTaskPeople::getInstance()->deleteByFields(['order_id' => $orderId]);
        parent::renderSuccessJson();
    }

    public function getNewTipsAction()
    {
        $account = AccountService::getInstance()->getAccountUserInfo(4);
        $counts = [];
        if ($account) {
            $ret = AccountsGroup::getInstance()->getPermsByGroupIds(explode(',', $account->groupid));
            $perms = [];

            foreach ($ret as $v) {
                $perms = array_merge($perms, explode(',', $v));
            }

            foreach ($perms as $v) {
                switch ($v) {
                    case 'order':
                        //$counts['order'] = Orders::getInstance()->countListByConditions(['order_status' => [94, 96]]);
                        break;
                    case 'dispatch':
                        $counts['dispatch'] = Orders::getInstance()->countListByConditions(['order_status' => 2]);
                        break;
                    case 'team':
                        $counts['team'] = Orders::getInstance()->countListByConditions(['order_status' => [3, 95, 99]]);
                        break;
                    case 'parts':
                        $counts['parts'] = Orders::getInstance()->countListByConditions(['pick_status' => 0, 'order_status' => [2, 3, 4, 94, 95]]);
                        break;
                    case 'quality':
                        $counts['quality'] = Orders::getInstance()->countListByConditions([
                                'order_status' => 4
                            ]) + Orders::getInstance()->countListByConditions(['order_status' => [4, 5], 'wash_status' => 0, 'need_wash' => 1]);
                        break;
                    case 'settlement':
                        $counts['settlement'] = Orders::getInstance()->countListByConditions([
                            'order_status' => [5, 6],
                            'pay_status' => 0
                        ]);
                        break;
                    case 'service':
                        $counts['service'] = Orders::getInstance()->countListByConditions(['order_status' => 6]);
                        break;
                }
            }
        }

        parent::renderSuccessJson([
            'counts' => $counts
        ]);

    }

    public function getAllConsultantListAction()
    {

        $res = Accounts::getInstance()->getUserNameByUids(Orders::getInstance()->getAllServiceConsultantUids());
        $list = [];

        foreach ($res as $k => $v) {
            $list[] = [
                'id' => $k,
                'name' => $v
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function getAllOperatorsAction()
    {
        $res = Accounts::getInstance()->getUserNameByUids(WorkTaskPeople::getInstance()->getAllOperators());
        $list = [];

        foreach ($res as $k => $v) {
            $list[] = [
                'id' => $k,
                'name' => $v
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }
}