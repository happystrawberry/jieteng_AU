<?php
/**
 *
 *
 * @since   2018/5/27 10:24
 */


namespace App\Models;


use App\Constants\CacheTimeoutConstants;

class Accounts extends BaseModel
{
    protected static $_TABLE = 'op_accounts';

    public function getUserNameByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $list = $this->getAllListByFields(['id' => $ids], ['id', 'username']);

        $data = [];
        foreach ($list as $v) {
            $data[$v->id] = $v->username;
        }

        $tmp = [];
        foreach ($ids as $id) {
            $tmp[$id] = isset($data[$id]) ? $data[$id] : '';
        }
        unset($data);

        return $tmp;
    }

    public function getUserNameByUid($uid)
    {
        $res = $this->getModelCache($uid);
        if ($res) {
            return $res;
        }
        $res = $this->getOneByField('id', $uid);

        if ($res) {
            $res = $res->username;
            $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MAX_CACHE, $uid);

            return $res;
        }

        return '';
    }

    public function getUserNameByUids($uids)
    {
        if (empty($uids)) {
            return [];
        }

        $data = [];

        $res = $this->getAllListByFields(['id' => $uids], ['id', 'username']);
        foreach ($res as $v) {
            $data[$v->id] = $v->username;
        }

        return $data;
    }


    public function getUserPhoneByUid($uid)
    {
        $res = $this->getModelCache($uid);
        if ($res) {
            return $res;
        }
        $res = $this->getOneByField('id', $uid);

        if ($res) {
            $res = $res->phone;
            $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MAX_CACHE, $uid);

            return $res;
        }

        return '';
    }

    public function getListBySearch($query, $offset, $limit)
    {
        $sql = 'SELECT * FROM ' . self::$_TABLE . ' WHERE (phone LIKE CONCAT("%",?,"%") OR username LIKE CONCAT("%",?,"%") OR ding_jobnumber LIKE CONCAT("%",?,"%")) AND display=1 ORDER BY id DESC LIMIT ' . $offset . ',' . $limit;

        return $this->selectByRawSql($sql, [$query, $query, $query]);
    }

    public function countListBySearch($query)
    {
        $sql = 'SELECT COUNT(*) AS num FROM ' . self::$_TABLE . ' WHERE (phone LIKE CONCAT("%",?,"%") OR username LIKE CONCAT("%",?,"%") OR ding_jobnumber LIKE CONCAT("%",?,"%")) AND display=1';

        return $this->selectOneByRawSql($sql, [$query, $query, $query]);
    }
}