<?php
/**
 *
 *
 * @since   2018/6/16 22:02
 */


namespace App\Models;


class WorkTaskPeople extends BaseModel
{
    protected static $_TABLE = 'op_worktask_people';

    public function updateAccountsByOrderId($orderId)
    {
        $res = WorkTask::getInstance()->getAllListByOnField(['order_id' => $orderId], 'target_account');

        $accounts = [];
        foreach ($res as $v) {
            if ($v) {
                $accounts = array_merge($accounts, explode(',', $v));
            }
        }
        $accounts = array_unique(array_filter($accounts));

        $this->deleteByFields(['order_id' => $orderId]);

        $values = [];
        foreach ($accounts as $u) {
            $values[] = [
                'order_id' => $orderId,
                'account_uid' => $u
            ];
        }

        $values ? $this->batchInsertByFields($values) : '';
    }

    public function getListByUid($uid, $beginTime = 0, $endTime = 0)
    {
        if (!$beginTime) {
            $beginTime = strtotime(date('Y-m-1'));
        }
        if (!$endTime) {
            $endTime = time();
        }

        $sql = <<<EOF
SELECT
  o.order_sn,o.id
FROM
  op_worktask_people p
  LEFT JOIN op_orders o
    ON p.`order_id` = o.`id`
WHERE p.`account_uid` = ?
  AND o.`pay_status` = 2
  AND o.`create_time` >=?
  AND o.`create_time` < ?
  ORDER BY o.id ASC
EOF;

        return $this->selectByRawSql($sql, [$uid, $beginTime, $endTime]);
    }

    public function countAccountByUnFinishOrder($accountId)
    {
        $sql = <<<EOF
SELECT
  COUNT(*) AS num
FROM
  op_worktask_people p
  LEFT JOIN op_orders o
    ON p.`order_id` = o.id
WHERE p.`account_uid` = ?
  AND o.`order_status` NOT IN (0, 6, 7)
EOF;

        return $this->selectOneByRawSql($sql, [$accountId])->num;
    }

    public function getAllOperators()
    {
        $sql = 'SELECT DISTINCT account_uid FROM op_worktask_people';

        $data = [];
        $res = $this->selectByRawSql($sql);

        foreach ($res as $v) {
            $data[] = $v->account_uid;
        }

        return $data;
    }
}