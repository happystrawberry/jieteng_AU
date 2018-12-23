<?php
/**
 *
 *
 * @since   2018/11/3 11:48
 */


namespace App\Models;


use App\Constants\BusinessConstants;

class Commission extends BaseModel
{
    protected static $_TABLE = 'op_commission';

    public function getDataByOrderIds($orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }

        $data = [];

        $sql = 'SELECT
  c.*,
  a.`username`
FROM
  op_commission c
  LEFT JOIN op_accounts a
    ON c.`account_id` = a.`id`
WHERE order_id IN ' . $this->assembleWhereInPlaceholders($orderIds);

        $res = $this->selectByRawSql($sql, $orderIds);

        foreach ($res as $v) {
            $data[$v->order_id][$v->op_type][$v->id] = $v;
        }

        return $data;
    }

    public function getDataByOrderId($orderId)
    {
        $res = $this->getDataByOrderIds([$orderId]);

        if (isset($res[$orderId])) {
            return $res[$orderId];
        }

        return [];
    }

    public function getCommissionRateByOrderId($orderId)
    {
        $list = $this->getListByConditions(['order_id' => $orderId], 0, BusinessConstants::MAX_RETURN_SIZE);

        $data = [];

        foreach ($list as $v) {
            $data[$v->item_id][$v->account_id] = $v->percentage;
        }

        return $data;
    }

    public function getCommissionByItemId($orderId, $itemId)
    {
        $commission = [];
        $res = Commission::getInstance()->getListByConditions(['order_id' => $orderId, 'item_id' => $itemId]);
        foreach ($res as $v) {
            $commission[$v->account_id] = round($v->percentage * 100, 2);
        }

        return $commission;
    }
}