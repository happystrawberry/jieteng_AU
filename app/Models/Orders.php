<?php
/**
 *
 *
 * @since   2018/6/5 22:35
 */

namespace App\Models;

use App\Constants\BusinessConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;

class Orders extends BaseModel
{
    protected static $_TABLE = 'op_orders';

    public function getOpTypeList($orderId)
    {
        $res = $this->getModelCache($orderId);

        if (is_array($res)) {
            return $res;
        }

        $res = array_values(array_unique(array_merge(
            OrdersParts::getInstance()->getAllListByOnField(['order_id' => $orderId, 'op_type>' => 0], 'op_type'),
            OrdersItems::getInstance()->getAllListByOnField(['order_id' => $orderId, 'op_type>' => 0], 'op_type')
        )));

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderId);

        return $res;
    }

    public function getOpTypeNameByOrderSn($orderSn, $upd = false)
    {
        $res = $this->getModelCache($orderSn);

        if (is_array($res) && !$upd) {
            return $res;
        }

        $orderInfo = Orders::getInstance()->getOneByField('order_sn', $orderSn);

        if (!$orderInfo) {
            return [];
        }

        $res = array_filter(array_values(array_unique(array_merge(
            OrdersParts::getInstance()->getAllListByOnField(['order_id' => $orderInfo->id, 'op_type>' => 0], 'op_type'),
            OrdersItems::getInstance()->getAllListByOnField(['order_id' => $orderInfo->id, 'op_type>' => 0], 'op_type')
        ))));

        $res = array_map(function ($i) {
            return isset(BusinessConstants::OP_ITEMS[$i]) ? BusinessConstants::OP_ITEMS[$i] : '';
        }, $res);

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderSn);

        return $res;
    }


    public function getOpTypeNameGroupByOrderSn($orderSn, $upd = false)
    {
        $res = $this->getModelCache($orderSn);

        if (is_array($res) && !$upd) {
            return $res;
        }

        $orderInfo = Orders::getInstance()->getOneByField('order_sn', $orderSn);

        if (!$orderInfo) {
            return [];
        }

        $res = array_filter(array_values(array_unique(array_merge(
            OrdersParts::getInstance()->getAllListByOnField(['order_id' => $orderInfo->id], 'op_type'),
            OrdersItems::getInstance()->getAllListByOnField(['order_id' => $orderInfo->id], 'op_type')
        ))));

        $res = array_values(array_map(function ($i) {
            return [
                'set' => isset(BusinessConstants::OP_ITEMS[$i]) ? BusinessConstants::DISPATCH_OP_TIMES_TASK_SET[$i] : '',
                'op_type' => $i,
                'op_name' => isset(BusinessConstants::OP_ITEMS[$i]) ? BusinessConstants::OP_ITEMS[$i] : ''
            ];
        }, $res));

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $orderSn);

        return $res;
    }

    public function getPayListByIds($orderIds)
    {
        return $this->getAllListByOnField(['pay_status' => 2, 'id' => $orderIds], 'id');
    }


    public function groupCount($filter = [], $wash = false, $pay = false, $beginTime = 0, $endTime = 0)
    {
        $cacheKey = CacheFunction::makeCacheKey($filter, $wash, $pay, $beginTime, $endTime);
        $res = $this->getModelCache();

        if ($res) {
            return $res;
        }

        $where = [];
        if ($beginTime) {
            $where[] = 'create_time>=' . $beginTime;
        }
        if ($endTime) {
            $where[] = 'create_time<=' . $endTime;
        }
        if ($where) {
            $where = ' WHERE ' . implode(' AND ', $where) . ' ';
        } else {
            $where = '';
        }

        $sql = 'SELECT order_status,COUNT(*) AS num FROM ' . self::$_TABLE . $where . ' GROUP BY order_status';

        $list = $this->selectByRawSql($sql, []);

        $tmp = [];
        $res = [];
        $total = 0;
        foreach ($list as $v) {
            if ($filter && !in_array($v->order_status, $filter)) {
                continue;
            }

            $tmp[$v->order_status] = [
                'name' => BusinessConstants::ORDER_STATUS[$v->order_status],
                'num' => $v->num,
                'order_status' => $v->order_status
            ];

            $total += $v->num;
        }

        foreach ($filter as $f) {
            if (!array_key_exists($f, $tmp)) {
                $res[$f] = [
                    'name' => BusinessConstants::ORDER_STATUS[$f],
                    'num' => 0,
                    'order_status' => $f
                ];
            } else {
                $res[$f] = $tmp[$f];
            }
        }

        if ($wash && !$pay && isset($res[4])) {
            $res[4]['name'] = '待质检';
        }

        if ($wash) {
            $conds = ['order_status' => [4, 5], 'wash_status' => 0, 'need_wash' => 1];
            if ($beginTime) {
                $conds['create_time>='] = $beginTime;
            }
            if ($endTime) {
                $conds['create_time<='] = $endTime;
            }
            $washTotal = Orders::getInstance()->countListByConditions($conds);
            $res[] = [
                'name' => '待洗车',
                'num' => $washTotal,
                'order_status' => 98
            ];
        }

        $payTotal = 0;
        if ($pay) {
            $payStatus = $this->groupCountByPay($beginTime, $endTime);
            $payTotalStatus = array_pop($payStatus);
            $payTotal = $payTotalStatus['num'];
            $res = array_merge($res, $payStatus);
        }

        $res[] = [
            'name' => '全部',
            'num' => $total + $payTotal,
            'order_status' => -1
        ];
        $res = array_values($res);
        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $cacheKey);

        return $res;
    }

    public function groupCountByPick()
    {
        $res = $this->getModelCache();

        if ($res) {
            return $res;
        }

        $sql = 'SELECT pick_status,COUNT(*) AS num FROM ' . self::$_TABLE . ' WHERE order_status IN (2, 3, 4, 94, 95) GROUP BY pick_status';

        $list = $this->selectByRawSql($sql, []);

        $res = [];
        $total = 0;
        foreach ($list as $v) {
            $res[] = [
                'name' => BusinessConstants::ORDER_PICK_STATUS[$v->pick_status],
                'num' => $v->num,
                'pick_status' => $v->pick_status
            ];

            $total += $v->num;
        }

        $res[] = [
            'name' => '全部',
            'num' => $total,
            'pick_status' => -1
        ];

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE);

        return $res;
    }

    public function groupCountByPay($beginTime = 0, $endTime = 0)
    {
        $cacheKey = $beginTime . '_' . $endTime;

        $res = $this->getModelCache($cacheKey);

        if ($res) {
            return $res;
        }

        $where = [];

        if ($beginTime) {
            $where[] = 'create_time>=' . $beginTime;
        }

        if ($endTime) {
            $where[] = 'create_time<=' . $endTime;
        }

        if ($where) {
            $where = ' AND ' . implode(' AND ', $where) . ' ';
        } else {
            $where = '';
        }

        $sql = 'SELECT pay_status,COUNT(*) AS num FROM ' . self::$_TABLE . ' WHERE order_status IN (5,6) ' . $where . ' GROUP BY pay_status';

        $list = $this->selectByRawSql($sql, []);

        $res = [];
        $total = 0;
        foreach ($list as $v) {
            $res[$v->pay_status] = [
                'name' => BusinessConstants::ORDER_PAY_STATUS[$v->pay_status],
                'num' => $v->num,
                'order_status' => '8' . $v->pay_status
            ];

            $total += $v->num;
        }

        foreach (BusinessConstants::ORDER_PAY_STATUS as $k => $v) {
            if (!isset($res[$k])) {
                $res[$k] = [
                    'name' => $v,
                    'num' => 0,
                    'order_status' => '8' . $k
                ];
            }
        }

        $res[] = [
            'name' => '全部',
            'num' => $total,
            'order_status' => ''
        ];

        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_MIN_CACHE, $cacheKey);

        return $res;
    }

    public function getBrandStat($brandId, $beginTime, $endTime)
    {
        $where = '';
        if ($brandId) {
            $where .= ' AND brand_id IN (' . $brandId . ') ';
        }

        $sql = <<<EOF
SELECT
  COUNT(brand_id) AS num,
  brand_id
FROM
  op_orders WHERE pay_status IN (1,2) AND pay_time>=? AND pay_time<? {$where}
GROUP BY brand_id
EOF;

        return $this->selectByRawSql($sql, [$beginTime, $endTime]);
    }

    public function getOrdersByOrderIds($orderIds)
    {
        $data = [];
        if (!empty($orderIds)) {
            $res = $this->getListByConditions(['id' => $orderIds]);

            foreach ($res as $v) {
                $data[$v->id] = $v;
            }

        }

        return $data;
    }

    public function sumIncome($conditions)
    {
        return self::assembleOrmWhereConditions($conditions)->sum('total_cost');
    }

    public function sumTotalCostGroupByOpType($beginTime, $endTime, $operator = '')
    {
        $where = [];

        $where[] = 'pay_time>=' . $beginTime;
        $where[] = 'pay_time<' . $endTime;

        if ($operator) {
            $where[] = 'o.service_consultant IN (' . $operator . ') ';
        }

        $where = $where ? (' AND ' . implode(' AND ', $where)) : '';

        $sql = <<<EOF
SELECT
  AVG(c.`commission_rate`),
  SUM(c.`fee`) AS fee,
  SUM(o.`total_cost`) AS total,
  c.`op_type`
FROM
  op_commission c
  LEFT JOIN op_orders o
    ON c.`order_id` = o.`id`
WHERE o.pay_status IN (1, 2)
  {$where} GROUP BY c.`op_type`
EOF;
        return $this->selectByRawSql($sql, []);
    }

    public function getAllServiceConsultantUids()
    {
        $sql = <<<EOF
SELECT DISTINCT service_consultant FROM op_orders 
EOF;
        $uids = [];
        $res = $this->selectByRawSql($sql, []);
        foreach ($res as $v) {
            $uids[] = $v->service_consultant;
        }

        return $uids;
    }

    /**
     *从orders表中找出orders_pay表中不存在的支付记录数据，然后将这些缺失数据同步到orders_pay表中。
     *
     */
    public function fixPayWayData()
    {
        $sql = <<<EOF
SELECT
  o.id,
  o.`pay_way`,
  o.`pay_time`,
  o.`total_cost`
FROM
  op_orders o
  LEFT JOIN op_orders_pay p
    ON o.id = p.`order_id`
WHERE p.`pay_way` IS NULL
  AND o.`pay_status` IN (1, 2)
EOF;

        $res = $this->selectByRawSql($sql);

        $values = [];
        foreach ($res as $v) {
            $values[] = [
                'order_id' => $v->id,
                'pay_way' => $v->pay_way,
                'pay_cost' => $v->total_cost,
                'dateline' => $v->pay_time
            ];
        }

        return $values ? OrdersPay::getInstance()->batchInsertByFields($values) : null;
    }

    public function getTotalIncomeByConditions(){

    }
}