<?php

namespace App\Jobs;

use App\Constants\BusinessConstants;
use App\Models\CreditLog;
use App\Models\Orders;
use App\Models\OrdersBill;
use App\Services\PayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlipayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const QUEUE_NAME = 'pay';

    private $orderId;

    private $orderSn;

    private $operator;

    private $waitTimeout = 60;

    private $checkInterval = 5;

    /**
     * Create a new job instance.
     * @var $orderId
     * @var $orderSn
     * @var $operator
     * @return void
     */
    public function __construct($orderId, $orderSn, $operator)
    {
        $this->orderId = $orderId;
        $this->orderSn = $orderSn;
        $this->operator = $operator;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;
        $total = ceil($this->waitTimeout / $this->checkInterval);
        while ($i <= $total) {
            //检查订单状态
            $payResult = PayService::getInstance()->alipay()->find([
                'out_trade_no' => $this->orderSn,
                'bill_type' => 'trade'
            ]);

            if (($payResult->code == 10000) && ($payResult->trade_status == 'TRADE_SUCCESS')) {
                //如果支付成功就修改订单状态
                OrdersBill::getInstance()->setBillDone($this->operator, $this->orderSn, $this->orderId, BusinessConstants::PAY_STATUS_ALIPAY, $payResult);

                $orderInfo = Orders::getInstance()->getOneByField('id', $this->orderId);
                if ($orderInfo) {
                    CreditLog::getInstance()->addCreditByPhone(
                        $this->operator,
                        $orderInfo->order_sn,
                        $orderInfo->phone,
                        floor($orderInfo->total_cost),
                        '消费奖励积分'
                    );
                }
                logger('#' . $this->orderSn . '#订单支付成功');
                break;
            }
            logger('#' . $this->orderSn . '#订单等待查询结果');
            sleep($this->checkInterval);
            $i++;
        }

    }
}
