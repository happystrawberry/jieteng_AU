<?php
/**
 *
 *
 * @since   2018/11/3 11:47
 */


namespace App\Models;


class CommissionConfig extends BaseModel
{
    protected static $_TABLE = 'op_commission_config';

    public function checkMinAndMax($min,$max){
        $sql = 'SELECT COUNT(*) FROM '.self::$_TABLE.' WHERE (min_turnover<=? AND ?<=max_turnover) OR';

    }
}