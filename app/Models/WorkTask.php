<?php
/**
 *
 *
 * @since   2018/6/11 22:01
 */


namespace App\Models;


use App\Constants\BusinessConstants;
use App\Constants\CacheTimeoutConstants;

class WorkTask extends BaseModel
{
    protected static $_TABLE = 'op_worktask';

    public function getTaskListByOrderId($orderId)
    {
        $res = $this->getAllListByFields(['order_id' => $orderId],
            ['id', 'op_type', 'target_account', 'target_team', 'is_start', 'is_done', 'is_redo', 'done_time']);
        foreach ($res as &$v) {
            $v->item_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';

            if ($v->target_team) {
                $v->operator_name = AccountsGroup::getInstance()->getGroupNameByGroupId($v->target_team);
            } elseif ($v->target_account) {
                $v->operator_name = Accounts::getInstance()->getUserNameByUid($v->target_account);
            }

            if (BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$v->op_type] == 'team') {
                $v->dispatch_name = AccountsGroup::getInstance()->getGroupNameByGroupId($v->target_team);
            } else {
                $v->dispatch_name = Accounts::getInstance()->getUserNameByUid($v->target_account);
            }
            $v->target_team = intval($v->target_team);
            $v->target_account = intval($v->target_account);
        }
        unset($v);

        return $res;
    }

    public function getFinishTime($orderId)
    {
        $sql = 'SELECT MAX(done_time) AS dt FROM ' . self::$_TABLE . ' WHERE is_done=1 AND order_id=? LIMIT 1';

        return $this->selectOneByRawSql($sql, [$orderId])->dt;
    }

    public function getOperatorNameByOrderId($orderId)
    {
        $res = $this->getModelCache($orderId);
        if ($res) {

            return $res;
        }

        $list = $this->getAllListByFields(['order_id' => $orderId], ['op_type', 'target_account', 'target_team']);

        $uids = [];
        foreach ($list as $v) {
            $uids = array_merge($uids, $v->target_account ? explode(',', $v->target_account) : []);
        }

        $userNames = Accounts::getInstance()->getUserNameByIds($uids);
        $groupNames = AccountsGroup::getInstance()->getAllGroupNameList();

        $data = [];
        foreach ($list as $v) {
            $name = '';
            if ($v->target_team) {
                $name .= $groupNames[$v->target_team];
            }
            if ($v->target_account) {
                $name = $name . '(' . implode(',', array_map(function ($u) use ($userNames) {
                        return $userNames[$u];
                    }, explode(',', $v->target_account))) . ')';
            }

            $data[$v->op_type] = $name;
        }

        $this->setModelCache($data, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $data;
    }

    public function ifAllTaskDone($orderId)
    {
        $res = $this->getModelCache($orderId);

        if (is_numeric($res)) {
            return $res;
        }

        $res = 0;
        if (WorkTask::getInstance()->getOneByField('order_id', $orderId) && !WorkTask::getInstance()->countListByConditions([
                'order_id' => $orderId,
                'is_done' => 0
            ])) {
            $res = 1;
        }
        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $res;
    }

    public function getWorkEndTime($orderId)
    {
        $res = $this->getModelCache($orderId);

        if ($res) {
            return $res;
        }

        $sql = 'SELECT MAX(done_time) AS endtime FROM ' . self::$_TABLE . ' WHERE order_id=? AND is_done=1';

        $res = $this->selectByRawSql($sql, [$orderId]);

        $res = $res ? $res[0]->endtime : 0;

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $res;
    }

    public function getPaymentsByOrderId($orderId)
    {
        $itemList = OrdersItems::getInstance()->getListByConditions(['order_id' => $orderId, 'enable' => 1], 0, BusinessConstants::MAX_RETURN_SIZE);

        //统计每个type的总费用
        $costs = [];
        $times = [];
        $allUids = [];
        foreach ($itemList as $v) {
            if ($v->operators) {
                $uids = explode(',', $v->operators);
                $uNum = count($uids);
                $allUids = array_unique(array_merge($allUids, $uids));
                foreach ($uids as $uid) {
                    if (isset($costs[$uid])) {
                        $costs[$uid] += $v->final_price / $uNum;
                        $times[$uid] += $v->item_cost / $uNum;
                    } else {
                        $costs[$uid] = $v->final_price / $uNum;
                        $times[$uid] = $v->item_cost / $uNum;
                    }
                }
            }
        }

        // 获取worktask数据
        $workTaskList = WorkTask::getInstance()->getAllListByFields(['order_id' => $orderId],
            ['id', 'op_type', 'is_start', 'is_done', 'target_account', 'target_team', 'is_start']);

        $workTasks = [];
        $workTaskStartStatus = [];
        $workTaskDoneStatus = [];
        foreach ($workTaskList as $v) {
            $workTasks[$v->op_type] = $v;
            $workTaskStartStatus[$v->op_type] = $v->is_start;
            $workTaskDoneStatus[$v->op_type] = $v->is_done;
        }

        foreach ($itemList as &$v) {
            $v->type_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            $v->dispatch_type = BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$v->op_type];
            $v->worktask_id = 0;
            $v->account_id = 0;
            $v->team_id = 0;
            if (isset($workTasks[$v->op_type])) {
                $v->worktask_id = $workTasks[$v->op_type]->id;
                if (BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$v->op_type] == 'people') {
                    $v->dispatch_team = '-';
                    $v->dispatch_user = Accounts::getInstance()->getUserNameByIds($workTasks[$v->op_type]->target_account);
                    $v->dispatch_uids = $workTasks[$v->op_type]->target_account ? [intval($workTasks[$v->op_type]->target_account)] : [];
                    $v->dispatch_commission = '100%';
                } else {
                    $v->team_id = $workTasks[$v->op_type]->target_team;
                    $v->dispatch_team = AccountsGroup::getInstance()->getGroupNameByGroupId($workTasks[$v->op_type]->target_team);
                    $v->dispatch_user = $v->operators ? implode(
                        ',',
                        Accounts::getInstance()->getUserNameByIds(array_filter(explode(',', $v->operators)))
                    ) : '';
                    $v->dispatch_uids = $v->operators ? array_map(function ($i) {
                        return intval($i);
                    }, explode(',', $v->operators)) : [];
                    $commission = Commission::getInstance()->getCommissionByItemId($v->order_id, $v->id);

                    $v->dispatch_commission = $v->operators ? array_map(function ($i) use ($commission) {
                        return isset($commission[$i]) ? ($commission[$i] . '%') : '0%';
                    }, explode(',', $v->operators)) : [];

                }

            } else {
                $v->dispatch_team = '-';
                $v->dispatch_user = '-';
            }

            $v->is_done = isset($workTaskDoneStatus[$v->op_type]) ? $workTaskDoneStatus[$v->op_type] : 0;
            $v->is_start = isset($workTaskStartStatus[$v->op_type]) ? $workTaskStartStatus[$v->op_type] : 0;


        }
        unset($v);
        $userNames = Accounts::getInstance()->getUserNameByIds(array_values($allUids));
        $payments = [];

        foreach ($allUids as $uid) {
            $payments[$uid] = [
                'username' => $userNames[$uid],
                'work_times' => $times[$uid],
                'work_pay' => $costs[$uid]
            ];
        }

        $res = [$payments, $itemList];

        return $res;
    }
}