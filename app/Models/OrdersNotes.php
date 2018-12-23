<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/12
 * Time: 10:21
 */

namespace App\Models;


use App\Constants\CacheTimeoutConstants;

class OrdersNotes extends BaseModel
{
    protected static $_TABLE = 'op_orders_notes';

    public function getNumByOrderId($orderId)
    {
        $res = $this->getModelCache($orderId);

        if ($res) {
            return $res;
        }

        $res = $this->countListByConditions(['order_id' => $orderId]);

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $res;
    }
}