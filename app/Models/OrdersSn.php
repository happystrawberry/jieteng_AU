<?php
/**
 *
 *
 * @since   2018/6/15 23:21
 */


namespace App\Models;


class OrdersSn extends BaseModel
{
    protected static $_TABLE = 'op_orders_sn';

    public function getPayOrderSn($orderId, $orderSn)
    {
        $id = $this->insertByFields(['order_id' => $orderId, 'dateline' => time()]);

        $payOrderSn = $orderSn . '_' . $id;
        $this->updateByFields([
            'pay_order_sn' => $payOrderSn
        ], ['id' => $id]);

        return $payOrderSn;
    }
}