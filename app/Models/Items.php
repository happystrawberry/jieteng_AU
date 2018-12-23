<?php
/**
 *
 *
 * @since   2018/5/31 21:50
 */


namespace App\Models;


class Items extends BaseModel
{
    protected static $_TABLE = 'op_items';

    public function getExistListByOpNumbers($opNumbers)
    {
        if (empty($opNumbers)) {
            return [];
        }

        return $this->_ORM->whereIn('op_number', $opNumbers)->pluck('op_number')->toArray();
    }

    public function deleteByOpNumbers($opNumbers)
    {
        if (empty($opNumbers)) {
            return false;
        }

        return $this->_ORM->whereIn('op_number', $opNumbers)->delete();
    }

    public function searchItems($opType, $query,$vehicleClass)
    {
        $sql = 'SELECT id,op_number,op_type,item_name,' . $vehicleClass . '_times,' . $vehicleClass . '_cost FROM ' . self::$_TABLE . ' WHERE (op_number=? OR item_name LIKE CONCAT("%",?,"%")) AND op_type=? AND enable=1';

        return $this->selectByRawSql($sql, [$query, $query, $opType]);
    }
}