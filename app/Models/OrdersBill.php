<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/15
 * Time: 14:42
 */

namespace App\Models;


class OrdersBill extends BaseModel
{
    protected static $_TABLE = 'op_orders_bill';

    public function setBillDone($operator, $payOrderSn, $orderId, $payWay, $payResult = null, $payUserPhone = '')
    {
        if (!Orders::getInstance()->updateByFields(
            [
                'order_status' => 6,
                'pay_way' => $payWay,
                'pay_status' => 2,
                'bill_operator' => $operator,
                'pay_order_sn' => $payOrderSn,
                'pay_time' => time()
            ],
            ['id' => $orderId, 'order_status' => 5, 'pay_status' => [0, 1]]
        )) {
            return false;
        }

        if (OrdersBill::getInstance()->getOneByField('order_id', $orderId)) {
            OrdersBill::getInstance()->deleteByFields(['order_id' => $orderId]);
        }

        // 只有支付宝和微信支付需要记录支付账单信息
        if ($payResult) {
            OrdersBill::getInstance()->insertByFields([
                'order_id' => $orderId,
                'pay_way' => $payWay,
                'trade_no' => $payResult->trade_no,
                'out_trade_no' => $payResult->out_trade_no,
                'buyer_pay_amount' => $payResult->buyer_pay_amount,
                'buyer_logon_id' => $payResult->buyer_logon_id,
                'buyer_user_id' => $payResult->buyer_user_id,
                'buyer_user_type' => $payResult->buyer_user_type,
                'fund_bill_list' => json_encode($payResult->fund_bill_list),
                'gmt_payment' => strtotime(isset($payResult->gmt_payment) ? $payResult->gmt_payment : $payResult->send_pay_date),
                'invoice_amount' => $payResult->invoice_amount,
                'point_amount' => $payResult->point_amount,
                'receipt_amount' => $payResult->receipt_amount,
                'total_amount' => $payResult->total_amount,
                'dateline' => time()
            ]);
        } else {
            OrdersBill::getInstance()->insertByFields([
                'order_id' => $orderId,
                'pay_way' => $payWay,
                'pay_user_phone' => $payUserPhone,
                'out_trade_no' => $payOrderSn,
                'dateline' => time()
            ]);
        }

        return true;
    }
}