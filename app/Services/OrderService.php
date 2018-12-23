<?php
/**
 *
 *
 * @since   2018/8/22 21:38
 */


namespace App\Services;


use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrdersParts;

class OrderService extends BaseService
{
    public function calcTotalCost($orderId)
    {
        $items = OrdersItems::getInstance()->countPay($orderId);
        $parts = OrdersParts::getInstance()->countPay($orderId);

        $order = Orders::getInstance()->getOneByField('id', $orderId);

        return [
            'total_times' => $items->times,
            'total_parts' => $parts->num,
            'times_cost' => $items->cost,
            'parts_cost' => $parts->cost,
            'other_cost' => $order->other_cost,
            'total_cost' => $items->cost + $parts->cost + $order->other_cost
        ];
    }

    public function refreshTotalCost($orderId)
    {
        $values = $this->calcTotalCost($orderId);

        Orders::getInstance()->updateByFields($values, ['id' => $orderId]);
    }
}