<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/11
 * Time: 15:27
 */

namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\AccountsGroup;
use App\Models\AccountsGroupItems;
use App\Models\Orders;
use App\Models\WorkTask;
use App\Models\WorkTaskPeople;
use App\Services\AccountService;

class PeopleController extends ApiBaseController
{
    public function syncAction()
    {
        set_time_limit(60);
        if (!AccountService::getInstance()->syncDingTalkUser()) {
            static::errorThrow('同步失败请重试');
        }

        parent::renderSuccessJson();
    }

    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $query = $this->getParameter('query', '');

        $total = Accounts::getInstance()->countListBySearch($query)->num;
        $list = [];
        if ($total) {
            $list = Accounts::getInstance()->getListBySearch($query, $pageOffset, $pageSize);
            $groupNames = AccountsGroup::getInstance()->getAllGroupNameList();
            foreach ($list as &$v) {
                $v->groupname = AccountsGroup::getInstance()->getGroupNameByGroupId($v->groupid);
            }
            unset($v);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }


    public function viewAction()
    {
        $id = $this->getIntegerParameter('id');

        $ret = Accounts::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('数据不存在');
        }

        $ret->groupname = AccountsGroup::getInstance()->getGroupNameByGroupId($ret->groupid);
        $ret->items = array_map(function ($i) {
            return BusinessConstants::OP_ITEMS[$i];
        }, AccountsGroupItems::getInstance()->getItemTypesByGroupId($ret->groupid));

        parent::renderSuccessJson([
            'info' => $ret
        ]);
    }

    public function maintainStatAction()
    {
        $id = $this->getIntegerParameter('id');
        $beginTime = $this->getParameter('begin_time', strtotime(date('Y-m-1')));
        $endTime = $this->getParameter('end_time', 0);

        $list = WorkTaskPeople::getInstance()->getListByUid($id, $beginTime, $endTime);

        //检查订单有效性，只有支付成功的订单才能结算
        $orderIds = [];
        foreach ($list as $v) {
            $orderIds[] = $v->id;
        }
        $hasPayOrderIds = Orders::getInstance()->getPayListByIds($orderIds);

        foreach ($list as &$v) {
            if (!in_array($v->id, $hasPayOrderIds)) {
                continue;
            }

            list($payments, $itemList) = WorkTask::getInstance()->getPaymentsByOrderId($v->id);

            $v->times = $payments[$id]['work_times'];
            $v->costs = $payments[$id]['work_pay'];
        }
        unset($v);

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function consultantStatAction()
    {
        $id = $this->getIntegerParameter('id');

        $beginTime = $this->getParameter('begin_time', strtotime(date('Y-m-1')));
        $endTime = $this->getParameter('end_time', 0);

        $conditions = [];

        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }

        if ($endTime) {
            $conditions['create_time<'] = $endTime;
        }
        $conditions['service_consultant'] = $id;
        $conditions['pay_status'] = 2;

        $list = Orders::getInstance()->getListByConditions(
            $conditions,
            0,
            BusinessConstants::MAX_RETURN_SIZE,
            'id',
            'desc',
            ['order_sn', 'total_cost']
        );

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }
}