<?php
/**
 *
 *
 * @since   2018/6/5 20:46
 */


namespace App\Models;


class BUser extends BaseModel
{
    protected static $_TABLE = 'user';

    protected static $_conn = 'mysql2';

    public function chargeWallet($operator, $uid, $orderSn, $cash, $cause)
    {
        $ret = BUser::getInstance()->getOneByField('id', $uid);
        $cash = abs($cash);
        if (!$ret) {
            return false;
        }

        $sql = 'UPDATE ' . self::$_TABLE . ' SET wallet_birthday=wallet_birthday-? WHERE id=? AND wallet_birthday>=?';

        if ($this->updateByRawSql($sql, [$cash, $uid, $cash])) {
            return BalanceLog::getInstance()->insertByFields([
                'uid' => $uid,
                'order_sn' => $orderSn,
                'is_add' => 0,
                'change_value' => $cash,
                'change_cause' => $cause,
                'balance' => $ret->wallet_birthday - $cash,
                'operator' => $operator,
                'dateline' => time()
            ]);
        }

        return false;
    }

    public function rechargeWallet($operator, $uid, $orderSn, $cash, $cause)
    {
        $ret = BUser::getInstance()->getOneByField('id', $uid);
        $cash = abs($cash);
        if (!$ret) {
            return false;
        }

        $sql = 'UPDATE ' . self::$_TABLE . ' SET wallet_birthday=wallet_birthday+? WHERE id=?';

        if ($this->updateByRawSql($sql, [$cash, $uid])) {
            return BalanceLog::getInstance()->insertByFields([
                'uid' => $uid,
                'order_sn' => $orderSn,
                'is_add' => 1,
                'change_value' => $cash,
                'change_cause' => $cause,
                'balance' => $ret->wallet_birthday + $cash,
                'operator' => $operator,
                'dateline' => time()
            ]);
        }

        return false;
    }

    public function refundToWallet($phone, $cash)
    {
        $sql = 'UPDATE ' . self::$_TABLE . ' SET wallet_birthday=wallet_birthday+? WHERE phone=?';

        return $this->updateByRawSql($sql, [$cash, $phone]);
    }

    public function addCreditByUid($uid, $credit)
    {
        $sql = 'UPDATE ' . self::$_TABLE . ' SET integral=integral+? WHERE id=?';

        return $this->updateByRawSql($sql, [$credit, $uid]);
    }

    public function reduceCreditByUid($uid, $credit)
    {
        $sql = 'UPDATE ' . self::$_TABLE . ' SET integral=integral-? WHERE id=?';

        return $this->updateByRawSql($sql, [$credit, $uid]);
    }

    public function searchVehicleNumber($vn){
        $sql =<<<EOF
SELECT
  u.`username`,
  u.`phone`,
  uv.`license_plate`,
  uvo.`name` AS owner_name,
  uvt.`name` AS travel_name,
  uv.`brand`,
  uv.`model`,
  uv.`vehicle_model`
FROM
  `user` u
  LEFT JOIN user_vehicle uv
    ON u.`id` = uv.`uid`
  LEFT JOIN user_vehicle_owner uvo
    ON uvo.uid = uv.`uid`
    AND uvo.`vehicle_id` = uv.`vehicle_id`
  LEFT JOIN user_vehicle_travel uvt
    ON uvt.uid = uv.`uid`
    AND uvt.`vehicle_id` = uv.`vehicle_id`
WHERE uv.`license_plate` LIKE CONCAT('%',?,'%')
EOF;

        return $this->selectByRawSql($sql,[$vn]);
    }
}