<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/7
 * Time: 16:56
 */

namespace App\Models;


use App\Constants\BusinessConstants;

class Parts extends BaseModel
{
    protected static $_TABLE = 'op_parts';

    public function searchParts($opType, $query)
    {
        if ($opType) {
            $sql = 'SELECT sn,parts_name,`spec`,unit,sell_price FROM '
                . self::$_TABLE . ' WHERE op_type=? AND ( sn=? OR parts_name LIKE CONCAT("%",?,"%") OR target_model LIKE CONCAT("%",?,"%") ) AND amount>0 AND enable=1';

            return $this->selectByRawSql($sql, [$opType, $query, $query, $query]);
        }

        $sql = 'SELECT sn,parts_name,`spec`,unit,sell_price FROM '
            . self::$_TABLE . ' WHERE ( sn=? OR parts_name LIKE CONCAT("%",?,"%") OR target_model LIKE CONCAT("%",?,"%") )  AND amount>0 AND enable=1';

        return $this->selectByRawSql($sql, [$query, $query, $query]);
    }

    public function getPartsIdByName($name)
    {
        return $this->getListByConditions([
            '%parts_name%' => $name
        ], 0, BusinessConstants::MAX_RETURN_SIZE)->pluck('id')->toArray();
    }

    public function reduceAmount($id, $num)
    {
        $sql = 'UPDATE ' . self::$_TABLE . ' SET amount=amount-? WHERE id=? AND enable=1 AND amount>=? ';

        return $this->updateByRawSql($sql, [$num, $id, $num]);
    }

    public function addAmountBySn($sn, $num)
    {
        $sql = 'UPDATE ' . self::$_TABLE . ' SET amount=amount+? WHERE sn=? AND enable=1';

        return $this->updateByRawSql($sql, [$num, $sn]);
    }

    public function getPartsByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }

        $data = [];

        $res = $this->getListByConditions(['id' => $ids], 0, count($ids));

        foreach ($res as $v) {
            $data[$v->id] = $v;
        }

        return $data;
    }

    public function getPartsBySns($sns)
    {
        if (empty($sns)) {
            return [];
        }

        $data = [];

        $res = $this->getListByConditions(['sn' => $sns], 0, count($sns));

        foreach ($res as $v) {
            $data[$v->sn] = $v;
        }

        return $data;
    }
}