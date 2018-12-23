<?php
/**
 *
 *
 * @since   2018/6/5 22:35
 */


namespace App\Models;


use App\Constants\CacheTimeoutConstants;

class OrdersItems extends BaseModel
{
    protected static $_TABLE = 'op_orders_items';


    public function getQualityEndTime($orderId)
    {
        $res = $this->getModelCache($orderId);

        if ($res) {
            return $res;
        }

        $sql = 'SELECT MAX(quality_time) AS endtime FROM ' . self::$_TABLE . ' WHERE order_id=? AND quality_status=1';

        $res = $this->selectOneByRawSql($sql, [$orderId]);

        $res = $res ? $res->endtime : 0;

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $res;
    }

    public function countPay($orderId)
    {
        $sql = 'SELECT SUM(item_times) AS times,SUM(final_price) AS cost FROM op_orders_items WHERE order_id=? AND enable=1';

        return $this->selectOneByRawSql($sql, [$orderId]);
    }

    public function getSummaryByOrderIds($orderIds)
    {
        if (empty($orderIds)) {
            return [0, 0, 0];
        }

        $sql = 'SELECT
  id,
  order_id,
  op_type,
  item_times,
  item_cost,
  final_price,
  operators,
  dateline
FROM
  op_orders_items
WHERE order_id IN ' . $this->assembleWhereInPlaceholders($orderIds);

        $data = [];

        $res = $this->selectByRawSql($sql, $orderIds);
        $totalCost = [];
        $totalTimes = [];
        foreach ($res as $v) {

            if (isset($totalCost[$v->order_id])) {
                $totalCost[$v->order_id] += $v->final_price;
            } else {
                $totalCost[$v->order_id] = $v->final_price;
            }

            if (isset($totalTimes[$v->order_id])) {
                $totalTimes[$v->order_id] += $v->item_times;
            } else {
                $totalTimes[$v->order_id] = $v->item_times;
            }

            $data[$v->order_id][$v->op_type][$v->id] = $v;
        }

        return [$data, $totalCost, $totalTimes];
    }

    public function getSummaryByOrderId($orderId)
    {
        list($data, $cost, $times) = $this->getSummaryByOrderIds([$orderId]);

        return [
            isset($data[$orderId]) ? $data[$orderId] : [],
            isset($cost[$orderId]) ? $cost[$orderId] : 0,
            isset($times[$orderId]) ? $times[$orderId] : 0
        ];
    }

}