<?php
/**
 *
 *
 * @since   2018/6/3 9:24
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Constants\CacheKeyConstants;
use App\Functions\CacheFunction;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\BalanceLog;
use App\Models\BUser;
use App\Models\BUserVehicle;
use App\Models\BUserVehicleOwner;
use App\Models\BUserVehicleTravel;
use App\Models\ConfigModel;
use App\Models\CreditLog;
use App\Models\CreditRule;
use App\Models\Orders;
use App\Services\SmsService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MemberController extends ApiBaseController
{
    public function sendSmsAction()
    {
        $phone = $this->getParameter('phone');

        if (!UtilFunction::checkMobileFormat($phone)) {
            static::errorThrow('手机号格式不合法');
        }

        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SMS_CODE, $phone);

        $randVerifyCode = rand(1000, 9999);

        $sms = new SmsService(env('SMS_APPKEY'), env('SMS_APPSECRET'));
        $ret = $sms->sendSMSTemplate(env('SMS_TEMPLATE'), [$phone], [$randVerifyCode]);

        if ($ret['code'] != 200) {
            static::errorThrow('发送短信验证码失败,错误信息:' . $ret['desc']);
        }

        \Cache::set($cacheKey, $randVerifyCode, 600);

        parent::renderSuccessJson();
    }

    public function saveInfoAction()
    {
        $uid = $this->getParameter('uid', 0);
        $memberName = $this->getParameter('member_name');
        $phone = $this->getParameter('phone');
        $verifyCode = $this->getIntegerParameter('verify_code', 0);
        $enable = $this->getParameter('enable', 1);

        if (!UtilFunction::checkMobileFormat($phone)) {
            static::errorThrow('手机号格式不正确');
        }
        $ret = BUser::getInstance()->getOneByField('phone', $phone);
        if ($ret && (!$uid || $ret->id != $uid)) {
            static::errorThrow('已经存在相同手机号请核对');
        }
        // 手机号不存在说明要验证手机号
        if (!$ret) {
            $code = \Cache::get($cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SMS_CODE, $phone));
            if (!$code || ($verifyCode !== intval($code))) {
                static::errorThrow('验证码验证失败');
            }
        }

        $values = [
            'username' => $memberName,
            'phone' => $phone,
            'status' => $enable ? 1 : 2
        ];

        if ($uid) {
            BUser::getInstance()->updateByFields($values, ['id' => $uid]);
        } else {
            $values['reg_time'] = time();
            $uid = BUser::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson([
            'uid' => $uid
        ]);
    }

    public function saveVehicleInfoAction()
    {
        $id = $this->getIntegerParameter('id', 0);
        $uid = $this->getParameter('uid');
        $vehicleNumber = $this->getParameter('vehicle_number');
        $vehicleNickName = $this->getParameter('vehicle_nickname', '');
        $vehicleFrame = $this->getParameter('vehicle_frame', 0);
        $brandId = $this->getIntegerParameter('brand_id');
        $vehicleId = $this->getIntegerParameter('vehicle_id');
        $vehicleModelId = $this->getIntegerParameter('vehicle_model_id');
        $currentKm = $this->getIntegerParameter('current_km', 0);
        $checkupDate = $this->getParameter('checkup_date', 0);
        $insuranceExpire = $this->getParameter('insurance_expire', 0);
        $insuranceCompany = $this->getParameter('insurance_company', '');
        $maintenanceKm = $this->getParameter('maintenance_km', 0);
        $maintenanceCycle = $this->getParameter('maintenance_cycle', 0);
        $maintenanceNextKm = $this->getParameter('maintenance_next_km', 0);
        $maintenanceNextDate = $this->getParameter('maintenance_next_date', 0);
        $licFrontImg = $this->getParameter('lic_front_img', '');
        $licReverseImg = $this->getParameter('lic_reverse_img', '');
        $idFrontImg = $this->getParameter('id_front_img', '');
        $idReverseImg = $this->getParameter('id_reverse_img', '');

        if (!BUser::getInstance()->getOneByField('id', $uid)) {
            static::errorThrow('用户uid不存在');
        }

        if (!ConfigModel::getInstance()->getOneByFields(['brand_id' => $brandId, 'vehicle_model_id' => $vehicleId, 'id' => $vehicleModelId])) {
            static::errorThrow('车型信息录入错误');
        }

        // 车辆管理及保养信息录入
        $values = [
            'uid' => $uid,
            'license_plate' => $vehicleNumber,
            'nickname' => $vehicleNickName,
            'frame' => $vehicleFrame,
            'brand' => $brandId,
            'model' => $vehicleId,
            'vehicle_model' => $vehicleModelId,
            'current_mileage' => $currentKm,
            'next_limited_time' => date('Y-m-d H:i:s', $checkupDate),
            'insurance_expire_time' => date('Y-m-d H:i:s', $insuranceExpire),
            'insurance_company' => $insuranceCompany,
            'maintain_mileage_cycle' => $maintenanceKm,
            'maintain_time_cycle' => $maintenanceCycle,
            'next_maintain_mileage' => $maintenanceNextKm,
            'next_maintain_time' => date('Y-m-d H:i:s', $maintenanceNextDate)
        ];

        if ($id) {
            BUserVehicle::getInstance()->updateByFields($values, ['vehicle_id' => $id]);
        } else {
            $id = BUserVehicle::getInstance()->insertByFields($values);
        }

        // 更新车主信息
        $values = [
            'uid' => $uid,
            'vehicle_id' => $id,
            'img_1' => $idFrontImg,
            'img_2' => $idReverseImg,
        ];
        if (BUserVehicleOwner::getInstance()->getOneByFields(['uid' => $uid, 'vehicle_id' => $id])) {
            BUserVehicleOwner::getInstance()->updateByFields($values, ['uid' => $uid, 'vehicle_id' => $id]);
        } else {
            BUserVehicleOwner::getInstance()->insertByFields($values);
        }

        // 更新行驶证信息
        $values = [
            'uid' => $uid,
            'vehicle_id' => $id,
            'img_1' => $licFrontImg,
            'img_2' => $licReverseImg,
        ];


        if (BUserVehicleTravel::getInstance()->getOneByFields(['uid' => $uid, 'vehicle_id' => $id])) {
            BUserVehicleTravel::getInstance()->updateByFields($values, ['uid' => $uid, 'vehicle_id' => $id]);
        } else {
            BUserVehicleTravel::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function getCreditLogAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $uid = $this->getIntegerParameter('uid');

        $total = CreditLog::getInstance()->countListByConditions(['uid' => $uid]);
        $list = [];
        if ($total) {
            $list = CreditLog::getInstance()->getListByConditions(['uid' => $uid], $pageOffset, $pageSize);

            foreach ($list as &$v) {
                $v->items = implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn));
                if ($v->order_sn && ($order = Orders::getInstance()->getOneByFields(['order_sn' => $v->order_sn]))) {
                    $v->vehicle_number = $order->vehicle_number;
                }
            }
            unset($v);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function getBalanceLogAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $uid = $this->getIntegerParameter('uid');

        $total = BalanceLog::getInstance()->countListByConditions(['uid' => $uid]);
        $list = [];
        if ($total) {
            $list = BalanceLog::getInstance()->getListByConditions(['uid' => $uid], $pageOffset, $pageSize);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }


    public function saveCreditRuleAction()
    {
        $id = $this->getParameter('id', 0);
        $ruleName = $this->getParameter('rule_name');
        $minCredit = $this->getIntegerParameter('min_credit', 0);
        $maxCredit = $this->getParameter('max_credit');
        $discount = $this->getParameter('discount');

        if (($ret = CreditRule::getInstance()->getOneByField('rule_name', $ruleName)) && (!$id || $ret->id != $id)) {
            static::errorThrow('存在重名的规则名称');
        }

        if ($minCredit >= $maxCredit) {
            static::errorThrow('积分范围输入有误');
        }

        if (($ret = CreditRule::getInstance()->ifInCreditRange($minCredit, $maxCredit)) && (!$id || $ret != $id)) {
            static::errorThrow('积分范围不能被包含在已设置积分规则内');
        }

        $values = [
            'rule_name' => $ruleName,
            'min_credit' => $minCredit,
            'max_credit' => $maxCredit,
            'discount' => $discount,
            'dateline' => time()
        ];

        if ($id) {
            CreditRule::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            CreditRule::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function getCreditRuleListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $total = CreditRule::getInstance()->countListByConditions();
        $list = [];
        if ($total) {
            $list = CreditRule::getInstance()->getListByConditions([], $pageOffset, $pageSize)->toArray();
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function deleteCreditRuleAction()
    {
        $id = $this->getIntegerParameter('id');

        if (!CreditRule::getInstance()->deleteByFields(['id' => $id])) {
            static::errorThrow('操作删除失败');
        }

        parent::renderSuccessJson();
    }

    public function updateCreditAction()
    {
        $id = $this->getIntegerParameter('id');
        $credit = $this->getIntegerParameter('credit');
        $this->mutexLock($id);

        if ($credit > 0) {
            if (!CreditLog::getInstance()->addCreditByUid($this->uid, '', $id, $credit, '手动添加积分')) {
                static::errorThrow('添加失败');
            }
        } elseif ($credit < 0) {
            if (!CreditLog::getInstance()->reduceCreditByUid($this->uid, '', $id, $credit, '手动减少积分')) {
                static::errorThrow('添加失败');
            }
        }

        parent::renderSuccessJson();
    }

    public function updateWalletAction()
    {
        $id = $this->getIntegerParameter('id');
        $cash = $this->getParameter('cash');
        $this->mutexLock($id);

        if ($cash > 0) {
            if (!BUser::getInstance()->rechargeWallet($this->uid, $id, '', $cash, '手动添加钱包余额')) {
                static::errorThrow('添加失败');
            }

        } elseif ($cash < 0) {
            if (!BUser::getInstance()->chargeWallet($this->uid, $id, '', $cash, '手动减少钱包余额')) {
                static::errorThrow('添加失败');
            }
        }

        parent::renderSuccessJson();

    }

    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));
        $export = $this->getParameter('export', 0);
        $query = $this->getParameter('query', '');
        $conditions = [];
        if ($query) {
            $conditions['%username%'] = $query;
            $conditions['%phone%'] = $query;
            $conditions['id'] = BUserVehicle::getInstance()->getAllListByOnField([
                '%license_plate%' => $query
            ], 'uid');
        }

        if ($export) {
            $pageSize = BusinessConstants::MAX_RETURN_SIZE;
        }

        $total = BUser::getInstance()->countListByConditions($conditions, true);
        $list = [];
        if ($total) {
            $list = BUser::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize, 'id', 'asc',
                ['id', 'username', 'phone', 'wallet_birthday', 'integral'], true);

            $uids = [];
            foreach ($list as $v) {
                $uids[] = $v->id;
            }

            $userVehicles = BUserVehicle::getInstance()->getUserVehicleInfoByUids($uids);

            foreach ($list as &$v) {
                $userVehicle = isset($userVehicles[$v->id]) ? $userVehicles[$v->id] : null;
                $v->vehicle_number = $userVehicle ? $userVehicle->license_plate : '';
                $v->vehicle_model = $userVehicle ? ConfigModel::getInstance()->getVehicleModelName($userVehicle->model) : '';
                $v->viplevel = CreditRule::getInstance()->getRuleNameByCredit($v->integral);
            }
            unset($v);
        }

        if ($export) {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();
            $workSheet->setTitle('会员表');
            $fields = [
                'id' => '用户编号',
                'username' => '用户名',
                'phone' => '手机号',
                'vehicle_number' => '车牌号',
                'vehicle_model' => '车型',
                'wallet_birthday' => '账户余额',
                'integral' => '用户积分',
                'viplevel' => '会员等级'
            ];

            $cells = array_values($fields);
            foreach ($cells as $k => $v) {
                $workSheet->getColumnDimension(chr(ord('A') + $k))->setWidth(20);
                $workSheet->setCellValueByColumnAndRow(($k + 1), 1, $v);
            }

            foreach ($list as $k => $v) {
                $i = 1;
                foreach ($fields as $f => $name) {
                    $workSheet->setCellValueByColumnAndRow($i, (2 + $k), $v->$f);
                    $i++;
                }
            }
            $write = IOFactory::createWriter($spreadSheet, 'Xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="会员信息' . date('Y-m-d_H:i') . '.xls"');
            header('Cache-Control: max-age=0');
            $write->save('php://output');

            return;
        }


        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }


    public function viewInfoAction()
    {
        $uid = $this->getIntegerParameter('uid');

        $user = BUser::getInstance()->getOneByField('id', $uid);

        if (!$user) {
            static::errorThrow('用户数据不存在');
        }
        unset($user->pwd);

        //获取车辆关系信息
        $userVehicle = BUserVehicle::getInstance()->getListByConditions(
            ['uid' => $uid], 0, BusinessConstants::MAX_RETURN_SIZE, 'vehicle_id');

        $vehicle = [];

        foreach ($userVehicle as $v) {
            $tmp = [];

            $tmp['vehicle_number'] = $v->license_plate;
            $v->next_limited_time = ($v->next_limited_time != '0000-00-00 00:00:00') ? strtotime($v->next_limited_time) : 0;
            $v->insurance_expire_time = ($v->insurance_expire_time != '0000-00-00 00:00:00') ? strtotime($v->insurance_expire_time) : 0;
            $v->next_maintain_time = ($v->next_maintain_time != '0000-00-00 00:00:00') ? strtotime($v->next_maintain_time) : 0;
            $tmp['vehicle'] = $v;

            $dl = BUserVehicleTravel::getInstance()->getOneByFields(['uid' => $uid, 'vehicle_id' => $v->vehicle_id]);
            $tmp['driving_license'] = [
                'img_1' => $dl ? $dl->img_1 : '',
                'img_2' => $dl ? $dl->img_2 : ''
            ];

            $dw = BUserVehicleOwner::getInstance()->getOneByFields(['uid' => $uid, 'vehicle_id' => $v->vehicle_id]);
            $tmp['vehicle_owner'] = [
                'img_1' => $dw ? $dw->img_1 : '',
                'img_2' => $dw ? $dw->img_2 : ''
            ];
            $vehicle[] = $tmp;
        }

        parent::renderSuccessJson([
            'user' => $user,
            'list' => $vehicle
        ]);
    }

    public function getServiceLogAction()
    {
        $vn = $this->getParameter('vehicle_number');

        $list = [];
        $res = Orders::getInstance()->getListByConditions([
            'vehicle_number' => $vn
        ], 0, BusinessConstants::MAX_RETURN_SIZE);

        foreach ($res as $v) {

            $list[] = [
                'order_id' => $v->id,
                'order_sn' => $v->order_sn,
                'pay_time' => $v->pay_time ? $v->pay_time : '',
                'service_items' => implode(',', Orders::getInstance()->getOpTypeNameByOrderSn($v->order_sn)),
                'vehicle_number' => $vn,
                'phone' => $v->phone,
                'total_cost' => $v->total_cost,
                'pay_status' => BusinessConstants::ORDER_PAY_STATUS[$v->pay_status],
                'order_status' => BusinessConstants::ORDER_STATUS[$v->order_status]
            ];

        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }
}