<?php
/**
 *
 *
 * @since   2018/5/27 15:45
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Accounts;
use App\Models\Orders;

class DashboardController extends ApiBaseController
{
    public function progressAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1), 12);
        $orderStatus = $this->getParameter('order_status', '');
        $os = [1, 2, 3, 4, 5, 99];
        $conditions = [];
        if ($orderStatus !== '') {
            $conditions['order_status'] = explode(',', $orderStatus);
        } else {
            $conditions['order_status'] = $os;
        }

        $vehicleNumber = $this->getParameter('vehicle_number', '');
        $beginTime = $this->getParameter('begin_time', 0);
        $endTime = $this->getParameter('end_time', 0);
        $orderSn = $this->getParameter('order_sn', '');
        $phone = $this->getParameter('phone', '');
        $vehicleOwner = $this->getParameter('vehicle_owner', '');
        $contactName = $this->getParameter('contact_name', '');

        if ($vehicleNumber) {
            $conditions['%vehicle_number%'] = $vehicleNumber;
        }
        if ($beginTime) {
            $conditions['create_time>='] = $beginTime;
        }
        if ($endTime) {
            $conditions['create_time<='] = $endTime;
        }
        if ($orderSn) {
            $conditions['%order_sn%'] = $orderSn;
        }
        if ($phone) {
            $conditions['%phone%'] = $phone;
        }

        if ($vehicleOwner) {
            $conditions['%vehicle_owner%'] = $vehicleOwner;
        }
        if ($contactName) {
            $conditions['%contact_name%'] = $contactName;
        }


        $list = [];
        $total = Orders::getInstance()->countListByConditions($conditions);
        if ($total) {
            $res = Orders::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
            foreach ($res as $v) {
                $list[] = [
                    'order_id' => $v->id,
                    'order_sn' => $v->order_sn,
                    'wait_number' => $v->wait_number,
                    'vehicle_number' => $v->vehicle_number,
                    'service_consultant' => Accounts::getInstance()->getUserNameByUid($v->service_consultant),
                    'arrive_time' => $v->arrive_time,
                    'plan_endtime' => $v->plan_endtime,
                    'contact_name' => $v->contact_name,
                    'vehicle_owner' => $v->vehicle_owner,
                    'order_status' => BusinessConstants::ORDER_STATUS[$v->order_status],
                    'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                    'progress' => in_array($v->order_status, [1, 2, 3, 4, 5]) ? round($v->order_status / 5 * 100) : 0,
                    'phone' => $v->phone,
                    'consultant_phone' => $v->consultant_phone,
                    'create_time'=>$v->create_time
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage, 12),
            'counts' => Orders::getInstance()->groupCount($os, false, false, $beginTime, $endTime)
        ]);
    }
}