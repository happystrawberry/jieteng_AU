<?php
/**
 *
 *
 * @since   2018/11/3 15:37
 */


namespace App\Models;


use App\Constants\BusinessConstants;

class OrdersPay extends BaseModel
{
    protected static $_TABLE = 'op_orders_pay';

    public function groupByPayWay($beginTime, $endTime, $payWay = '')
    {
        $where = '';
        if ($payWay) {
            $where .= ' AND pay_way IN (' . $payWay . ') ';
        }

        $sql = <<<EOF
SELECT
   SUM(pay_cost) AS amount,
  pay_way
FROM
  op_orders_pay WHERE dateline>=? AND dateline<=? {$where}
GROUP BY pay_way
EOF;

        $res = $this->selectByRawSql($sql, [$beginTime, $endTime]);

        $data = [];
        foreach ($res as $v) {
            $data[] = [
                'pay_way' => BusinessConstants::PAY_STATUS_SET[$v->pay_way],
                'amount' => $v->amount
            ];
        }

        return $data;
    }

    public function countListByTimeAndPayWay($beginTime, $endTime, $payWay = '')
    {
        $where = '';
        if ($payWay) {
            $where .= ' AND p.pay_way IN (' . $payWay . ') ';
        }

        $sql = <<<EOF
SELECT COUNT(DISTINCT p.`order_id`) AS num FROM op_orders_pay p LEFT JOIN op_orders o ON p.`order_id`=o.`id`  WHERE p.dateline>=? AND p.dateline<=? {$where}
EOF;

        return $this->selectOneByRawSql($sql, [$beginTime, $endTime])->num;
    }

    public function getListByTimeAndPayWay($beginTime, $endTime, $payWay = '')
    {
        $where = '';
        if ($payWay) {
            $where .= ' AND p.pay_way IN (' . $payWay . ') ';
        }

        $sql = <<<EOF
SELECT o.`order_sn`,p.* FROM op_orders_pay p LEFT JOIN op_orders o ON p.`order_id`=o.`id` WHERE p.dateline>=? AND p.dateline<=? {$where} ORDER BY p.order_id DESC
EOF;

        $res = $this->selectByRawSql($sql, [$beginTime, $endTime]);

        $tmp = [];
        $ids = [];
        foreach ($res as $v) {
            $tmp[$v->order_id][$v->pay_way] = $v;
            $ids[$v->order_id] = $v;
        }


        $data = [];
        foreach ($ids as $id => $v) {
            $payWays = [];
            $wayCosts = [];
            foreach (BusinessConstants::PAY_STATUS_SET as $k => $p) {
                $payWays[] = [
                    'name' => $p,
                    'pay_way' => $k,
                    'cost' => isset($tmp[$id][$k]) ? $tmp[$id][$k]->pay_cost : 0
                ];
                $wayCosts[$k] = isset($tmp[$id][$k]) ? $tmp[$id][$k]->pay_cost : 0;
            }
            $data[] = [
                'order_id' => $id,
                'order_sn' => $v->order_sn,
                'pay_time' => $v->dateline,
                'pay_ways' => $payWays,
                'way_cost' => $wayCosts
            ];
        }

        return $data;
    }
}