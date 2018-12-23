<?php
/**
 *
 *
 * @since   2018/11/3 12:05
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\CommissionConfig;
use App\Models\Setting;

class SettingController extends ApiBaseController
{
    public function commissionListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1), 12);

        $list = [];
        $total = CommissionConfig::getInstance()->countListByConditions([]);

        if ($total) {
            $list = CommissionConfig::getInstance()->getListByConditions([], $pageOffset, $pageSize, 'id', 'ASC');
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage, $pageSize),
        ]);
    }

    public function saveCommissionAction()
    {
        $id = $this->getIntegerParameter('id', 0);
        $min = $this->getParameter('min');
        $max = $this->getParameter('max');
        $rate = $this->getParameter('rate');

        if ($min >= $max) {
            static::errorThrow('营业收入值设定有误!');
        }

        if (($ret = CommissionConfig::getInstance()->getOneByFields(
                [
                    'min_turnover<=' => $max,
                    'max_turnover>=' => $min
                ]
            )) && (!$id || ($ret->id != $id))) {
            static::errorThrow('营业收入范围设定有误，请检查!');
        }

        $values = [
            'min_turnover' => $min,
            'max_turnover' => $max,
            'commission_rate' => $rate,
            'dateline' => time()
        ];

        if ($id) {
            CommissionConfig::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            CommissionConfig::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function deleteCommissionAction()
    {
        $id = $this->getIntegerParameter('id');

        CommissionConfig::getInstance()->deleteByFields(['id' => $id]);

        parent::renderSuccessJson();
    }

    public function editTaxRateAction()
    {
        $val = $this->getParameter('val');
        Setting::getInstance()->updateValByKey(BusinessConstants::SETTING_KEY_TAXRATE, $val);

        parent::renderSuccessJson();
    }

    public function getTaxRateAction()
    {

        $res = Setting::getInstance()->getOneByField('skey', BusinessConstants::SETTING_KEY_TAXRATE);

        parent::renderSuccessJson([
            'value' => $res ? $res->sval : 0
        ]);
    }
}