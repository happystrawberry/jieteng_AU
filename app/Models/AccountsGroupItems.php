<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/11
 * Time: 16:43
 */

namespace App\Models;


use App\Constants\BusinessConstants;
use App\Constants\CacheTimeoutConstants;

class AccountsGroupItems extends BaseModel
{
    protected static $_TABLE = 'op_accounts_group_items';

    public function updateItemsByGroupId($groupId, $items)
    {

        $items = array_filter($items);

        if (empty($items)) {
            return;
        }

        $this->deleteByFields(['group_id' => $groupId]);

        $values = [];

        foreach ($items as $v) {

            if (!array_key_exists($v, BusinessConstants::OP_ITEMS)) {
                continue;
            }

            $values[] = [
                'group_id' => $groupId,
                'item_type' => $v
            ];
        }

        return $this->insert($values);
    }

    public function getGroupListByTypeId($type)
    {
        $sql = <<<EOF
SELECT
    i.`group_id`,g.`group_name`
  FROM
    op_accounts_group_items i
    LEFT JOIN op_accounts_group g
      ON i.`group_id` = g.`id`
  WHERE g.`enable` = 1
    AND i.`item_type` = ?
EOF;

        return $this->selectByRawSql($sql, [$type]);
    }

    public function getItemTypeByGroupIds($gids)
    {
        $res = $this->getAllListByFields(['group_id' => $gids], ['group_id', 'item_type']);

        $data = [];

        foreach ($res as $v) {
            $data[$v->group_id][$v->item_type] = BusinessConstants::OP_ITEMS[$v->item_type];
        }

        return $data;
    }

    public function getItemTypesByGroupId($gid)
    {
        if (!$gid) {
            return [];
        }

        return array_unique($this->getAllListByOnField(['group_id' => explode(',', $gid)], 'item_type'));
    }

    public function getAccountListByOpType($opType)
    {
        $cache = $this->getModelCache($opType);

        if ($cache) {
            return $cache;
        }

        $sql = <<<EOF
SELECT
  id,
  username
FROM
  op_accounts a
WHERE id IN
  (SELECT
    gs.`uid`
  FROM
    op_account_groups gs
    LEFT JOIN op_accounts_group_items i
      ON gs.`group_id` = i.`group_id`
    LEFT JOIN op_accounts_group g
      ON i.`group_id` = g.`id`
  WHERE g.`enable` = 1
    AND i.`item_type` = ?)
  AND a.`enable` = 1 AND a.`display` = 1
EOF;

        $cache = $this->selectByRawSql($sql, [$opType]);

        $this->setModelCache($cache, CacheTimeoutConstants::LOCAL_MIN_CACHE, $opType);

        return $cache;
    }
}