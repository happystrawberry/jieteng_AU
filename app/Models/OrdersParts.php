<?php
/**
 *
 *
 * @since   2018/6/5 22:36
 */


namespace App\Models;


class OrdersParts extends BaseModel
{
    protected static $_TABLE = 'op_orders_parts';

    public function countPay($orderId)
    {
        $sql = 'SELECT SUM(amount) AS num,SUM(final_price) AS cost FROM op_orders_parts WHERE order_id=? AND parts_status IN(0,1) AND enable=1';

        return $this->selectOneByRawSql($sql, [$orderId]);
    }

    public function getStat($opType, $beginTime, $endTime)
    {
        $where = '';

        if ($opType) {
            $where .= ' AND p.op_type IN (' . $opType . ') ';
        }

        $sql = <<<EOF
SELECT
  SUM(p.amount) AS num,
  op_type
FROM
  op_orders_parts p LEFT JOIN op_orders o ON p.`order_id`=o.`id`
WHERE p.parts_status = 1 AND o.`pay_time`>=? AND o.pay_time<? AND o.`pay_status` IN (1,2) AND p.enable=1 {$where}
GROUP BY p.op_type
EOF;

        return $this->selectByRawSql($sql, [$beginTime, $endTime]);
    }

    public function getListByStat($opType, $beginTime, $endTime, $offset, $size)
    {
        $where = '';

        if ($opType) {
            $where .= ' AND p.op_type IN (' . $opType . ') ';
        }

        $sql = <<<EOF
SELECT
    o.order_sn,SUM(p.amount) AS amount,p.update_time,p.order_id
FROM
  op_orders_parts p LEFT JOIN op_orders o ON p.`order_id`=o.`id`
WHERE p.parts_status = 1 AND o.`pay_time`>=? AND o.pay_time<? AND o.`pay_status` IN (1,2)  AND p.enable=1 {$where} GROUP BY o.order_sn LIMIT {$offset},{$size}
EOF;

        return $this->selectByRawSql($sql, [$beginTime, $endTime]);
    }

    public function countListByStat($opType, $beginTime, $endTime)
    {
        $where = '';

        if ($opType) {
            $where .= ' AND p.op_type IN (' . $opType . ') ';
        }

        $sql = <<<EOF
SELECT
  COUNT(DISTINCT  p.order_id) AS num
FROM
  op_orders_parts p LEFT JOIN op_orders o ON p.`order_id`=o.`id`
WHERE p.parts_status = 1 AND o.`pay_time`>=? AND o.pay_time<? AND o.`pay_status` IN (1,2) AND p.enable=1 {$where}
EOF;

        return $this->selectOneByRawSql($sql, [$beginTime, $endTime])->num;
    }
}