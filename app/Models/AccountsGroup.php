<?php
/**
 *
 *
 * @since   2018/5/27 17:36
 */


namespace App\Models;


use App\Constants\CacheTimeoutConstants;

class AccountsGroup extends BaseModel
{
    protected static $_TABLE = 'op_accounts_group';

    private static $_cache = [];

    public function getEnableGroupById($id)
    {
        return $this->getOneByFields([
            'id' => $id,
            'enable' => 1
        ]);
    }

    public function getEnableGroupByIds($ids)
    {
        return $this->getAllListByOnField([
            'id' => $ids,
            'enable' => 1
        ], 'id');
    }


    public function getAllGroupNameList($enable = false)
    {
        if (static::$_cache) {
            return static::$_cache;
        }

        $res = $this->getModelCache($enable);

        if ($res) {
            return static::$_cache = $res;
        }

        $conds = [];

        if ($enable) {
            $conds['enable'] = 1;
        }
        $list = $this->getAllListByFields($conds, ['id', 'group_name']);

        $data = [];

        foreach ($list as $v) {
            $data[$v->id] = $v->group_name;
        }
        $this->setModelCache($data, CacheTimeoutConstants::LOCAL_MIDDLE_CACHE, $enable);

        return static::$_cache = $data;
    }

    public function getGroupNameByGroupId($groupId)
    {
        if (!$groupId) {
            return '';
        }

        $groupId = is_array($groupId) ? $groupId : array_unique(array_filter(explode(',', $groupId)));
        $groupNames = $this->getAllGroupNameList();

        return $groupId ? implode(', ', array_map(function ($v) use ($groupNames) {
            return isset($groupNames[$v]) ? $groupNames[$v] : '';
        }, $groupId)) : '';
    }

    public function getPermsByGroupIds($gids)
    {
        return $this->getAllListByOnField(['enable' => 1, 'id' => $gids], 'perms');
    }
}