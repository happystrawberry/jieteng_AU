<?php
/**
 * 通用支付服务类
 *
 * @since   2018/6/16 10:48
 */


namespace App\Services;


use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;
use App\Models\BUser;
use App\Models\Orders;
use App\Models\OrdersBill;
use Yansongda\Pay\Pay;

class PayService extends BaseService
{
    public function alipay()
    {
        return Pay::alipay([
            'app_id' => env('ALIPAY_APPID'),
            'notify_url' => env('ALIPAY_NOTIFY_URL'),
            'ali_public_key' => env('ALIPAY_PUBLIC_KEY'),
            'private_key' => env('ALIPAY_PRIVATE_KEY'),
            'log' => [
                'file' => storage_path('logs/alipay.log'),
                'level' => 'warning'
            ],
            'mode' => env('ALIPAY_MODE')
        ]);
    }

    public function setPayLock($orderId)
    {
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::PAY_LOCK_STATUS, $orderId);

        if (\Cache::has($cacheKey)) {
            return false;
        }

        \Cache::set($cacheKey, 1, CacheTimeoutConstants::LOCAL_CACHE);

        return true;
    }

    public function refundByAliPay($orderId, $payOrderSn, $totalCost)
    {
        $ret = self::alipay()->refund([
            'out_trade_no' => $payOrderSn,
            'refund_amount' => $totalCost
        ]);

        if ($ret->code == 10000 && $ret->msg == 'Success') {
            OrdersBill::getInstance()->updateByFields([
                'refund_time' => strtotime($ret->gmt_refund_pay),
                'refund_fee' => $ret->refund_fee,
                'refund_item_list' => json_encode($ret->refund_detail_item_list),
                'refund_change' => $ret->fund_change,
            ], ['order_id' => $orderId]);

            //设定退款
            Orders::getInstance()->updateByFields(['pay_status' => 3], [
                'id' => $orderId
            ]);

            return true;
        }
        logger('#' . $payOrderSn . '# 退款操作失败:' . $ret->msg);

        return false;
    }

    public function refundByCash($orderId)
    {
        OrdersBill::getInstance()->updateByFields(['refund_time' => time()], ['order_id' => $orderId]);

        return Orders::getInstance()->updateByFields(['pay_status' => 3], [
            'id' => $orderId
        ]);
    }

    public function refundByVipCard($orderId, $cash)
    {
        $ret = OrdersBill::getInstance()->getOneByField('order_id', $orderId);
        if (!$ret || !$ret->pay_user_phone) {
            return false;
        }

        if (BUser::getInstance()->refundToWallet($ret->pay_user_phone, $cash)) {

            OrdersBill::getInstance()->updateByFields(['refund_time' => time(), 'refund_fee' => $cash], ['order_id' => $orderId]);

            Orders::getInstance()->updateByFields(['pay_status' => 3], [
                'id' => $orderId
            ]);

            return true;
        }

        return false;
    }
}