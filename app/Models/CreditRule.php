<?php
/**
 *
 *
 * @since   2018/6/3 22:58
 */


namespace App\Models;


class CreditRule extends BaseModel
{
    protected static $_TABLE = 'op_credit_rule';

    public function ifInCreditRange($min, $max)
    {
        $res = $this->getOneByFields([
            'min_credit<=' => $min,
            'max_credit>=' => $min
        ]);

        if ($res) {
            return $res->id;
        }

        $res = $this->getOneByFields([
            'min_credit<=' => $max,
            'max_credit>=' => $max
        ]);

        if ($res) {
            return $res->id;
        }

        return 0;
    }

    public function getCreditRuleByCredit($credit)
    {
        $ret = $this->getOneByFields([
            'min_credit<=' => $credit,
            'max_credit>=' => $credit
        ]);

        return $ret;
    }

    public function getRuleNameByCredit($credit)
    {
        $ret = $this->getOneByFields([
            'min_credit<=' => $credit,
            'max_credit>=' => $credit
        ]);

        return $ret ? $ret->rule_name : '';
    }
}