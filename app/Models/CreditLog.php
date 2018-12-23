<?php
/**
 *
 *
 * @since   2018/6/3 22:58
 */


namespace App\Models;


class CreditLog extends BaseModel
{
    protected static $_TABLE = 'op_credit_log';

    public function addCreditByPhone($operator, $orderSn, $phone, $addCredit, $cause = '')
    {
        BaseModel::getInstance()->beginTransaction();
        $addCredit = abs($addCredit);
        $ret = BUser::getInstance()->getOneByField('phone', $phone);

        $uid = $ret ? $ret->id : 0;

        if ($ret) {
            BUser::getInstance()->addCreditByUid($ret->id, $addCredit);
        }

        $this->insertByFields([
            'uid' => $uid,
            'phone' => $phone,
            'order_sn' => $orderSn,
            'change_cause' => $cause,
            'change_value' => $addCredit,
            'credit' => $ret ? ($ret->integral + $addCredit) : 0,
            'operator' => $operator,
            'dateline' => time()
        ]);

        BaseModel::getInstance()->commitTransaction();
    }


    public function addCreditByUid($operator, $orderSn, $uid, $addCredit, $cause = '')
    {
        BaseModel::getInstance()->beginTransaction();
        $addCredit = abs($addCredit);
        $ret = BUser::getInstance()->getOneByField('id', $uid);

        if (!$ret) {
            return false;
        }

        if (BUser::getInstance()->addCreditByUid($ret->id, $addCredit)) {
            $this->insertByFields([
                'uid' => $uid,
                'phone' => $ret->phone,
                'order_sn' => $orderSn,
                'change_cause' => $cause,
                'change_value' => $addCredit,
                'credit' => $ret ? ($ret->integral + $addCredit) : 0,
                'operator' => $operator,
                'dateline' => time()
            ]);
        }

        BaseModel::getInstance()->commitTransaction();

        return true;
    }


    public function reduceCreditByPhone($operator, $orderSn, $phone, $cause = '')
    {
        BaseModel::getInstance()->beginTransaction();

        $creditLog = CreditLog::getInstance()->getOneByFields(['order_sn' => $orderSn, 'is_add' => 1]);

        if (!$creditLog) {
            return;
        }

        $ret = BUser::getInstance()->getOneByField('phone', $phone);

        $uid = $ret ? $ret->id : 0;

        if ($ret) {
            BUser::getInstance()->reduceCreditByUid($ret->id, $creditLog->change_value);
        }

        $this->insertByFields([
            'uid' => $uid,
            'phone' => $phone,
            'order_sn' => $orderSn,
            'change_cause' => $cause,
            'change_value' => $creditLog->change_value,
            'is_add' => 0,
            'credit' => $ret ? ($ret->integral - $creditLog->change_value) : -$creditLog->change_value,
            'operator' => $operator,
            'dateline' => time()
        ]);

        BaseModel::getInstance()->commitTransaction();
    }


    public function reduceCreditByUid($operator, $orderSn, $uid, $credit, $cause = '')
    {
        BaseModel::getInstance()->beginTransaction();
        $credit = abs($credit);
        $ret = BUser::getInstance()->getOneByField('id', $uid);

        if (!$ret) {
            return false;
        }

        if (BUser::getInstance()->reduceCreditByUid($ret->id, $credit)) {
            $this->insertByFields([
                'uid' => $uid,
                'phone' => $ret->phone,
                'order_sn' => $orderSn,
                'change_cause' => $cause,
                'change_value' => $credit,
                'is_add' => 0,
                'credit' => $ret->integral - $credit,
                'operator' => $operator,
                'dateline' => time()
            ]);
        }

        BaseModel::getInstance()->commitTransaction();

        return true;
    }

}