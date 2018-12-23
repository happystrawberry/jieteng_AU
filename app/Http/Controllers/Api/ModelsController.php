<?php
/**
 *
 *
 * @since   2018/6/3 9:25
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\ConfigBrand;
use App\Models\ConfigModel;
use App\Models\ConfigVehicleModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ModelsController extends ApiBaseController
{
    public function brandListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $name = $this->getParameter('name', '');
        $conds = [];

        if ($name) {
            $conds['%brand_name%'] = $name;
        }

        $total = ConfigBrand::getInstance()->countListByConditions($conds);

        $list = [];
        if ($total) {
            $list = ConfigBrand::getInstance()->getListByConditions($conds, $pageOffset, $pageSize);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function saveBrandAction()
    {
        $id = $this->getParameter('id', 0);
        $brandName = $this->getParameter('brand_name');
        $brandPic = $this->getParameter('img', '');
        $pinyin = strtoupper(substr(pinyin_abbr($brandName), 0, 1));

        if (($ret = ConfigBrand::getInstance()->getOneByField('brand_name', $brandName)) && (!$id || ($ret->id != $id))) {
            static::errorThrow('存在重复的品牌名');
        }

        $values = [
            'brand_name' => $brandName,
            'pinyin' => $pinyin
        ];

        if ($brandPic || !$id) {
            $values['img'] = $brandPic;
        }

        if ($id) {
            ConfigBrand::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            ConfigBrand::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function deleteBrandAction()
    {
        $id = $this->getParameter('id');

        if (!ConfigBrand::getInstance()->deleteByFields(['id' => $id])) {
            static::errorThrow('操作删除失败');
        }

        parent::renderSuccessJson();
    }

    public function vehicleListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $query = $this->getParameter('query', '');

        $conditions = [];
        if ($query) {
            $conditions['%vehicle_name%'] = $query;
        }

        $total = ConfigVehicleModel::getInstance()->countListByConditions($conditions);
        $list = [];
        if ($total) {
            $list = ConfigVehicleModel::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function saveVehicleAction()
    {
        $id = $this->getParameter('id', 0);
        $brandId = $this->getParameter('brand_id');
        $vehicleName = $this->getParameter('vehicle_name');

        if (!ConfigBrand::getInstance()->getOneByField('id', $brandId)) {
            static::errorThrow('品牌不存在');
        }

        if (($ret = ConfigVehicleModel::getInstance()->getOneByFields(['brand_id' => $brandId, 'vehicle_name' => $vehicleName])) && (!$id || $ret->id != $id)) {
            static::errorThrow('存在同名车型名称');
        }

        $values = [
            'brand_id' => $brandId,
            'vehicle_name' => $vehicleName
        ];

        if ($id) {
            ConfigVehicleModel::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            ConfigVehicleModel::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function deleteVehicleAction()
    {
        $id = $this->getParameter('id');

        if (!ConfigVehicleModel::getInstance()->deleteByFields(['id' => $id])) {
            static::errorThrow('操作删除失败');
        }

        parent::renderSuccessJson();
    }

    public function typeListAction()
    {
        $query = $this->getParameter('query', '');
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $total = ConfigModel::getInstance()->countList($query);

        $list = [];
        if ($total) {
            $list = ConfigModel::getInstance()->getList($query, $pageOffset, $pageSize);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function saveTypeAction()
    {
        $id = $this->getParameter('id', 0);
        $brandId = $this->getParameter('brand_id');
        $vehicleId = $this->getParameter('vehicle_id');
        $modelYear = $this->getParameter('model_year');
        $modelName = $this->getParameter('model_name');
        $modelClass = $this->getParameter('model_class');

        if (!ConfigVehicleModel::getInstance()->getOneByFields(['id' => $vehicleId, 'brand_id' => $brandId])) {
            static::errorThrow('车型品牌选择错误');
        }

        if (($ret = ConfigModel::getInstance()->getOneByFields([
                'brand_id' => $brandId,
                'vehicle_model_id' => $vehicleId,
                'model_name' => $modelName
            ])) && (!$id || $ret->id != $id)) {
            static::errorThrow('存在同名车型名称');
        }

        if (!in_array(strtoupper($modelClass), BusinessConstants::VEHICLE_MODEL_CLASS)) {
            static::errorThrow('车型等级选择错误');
        }

        $values = [
            'brand_id' => $brandId,
            'vehicle_model_id' => $vehicleId,
            'model_name' => $modelName,
            'year' => $modelYear,
            'model_class' => $modelClass
        ];

        if ($id) {
            ConfigModel::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            ConfigModel::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function deleteTypeAction()
    {
        $id = $this->getParameter('id');

        if (!ConfigModel::getInstance()->deleteByFields(['id' => $id])) {
            static::errorThrow('操作删除失败');
        }

        parent::renderSuccessJson();
    }

    public function getBrandListAction()
    {
        $res = ConfigBrand::getInstance()->getListByConditions([], 0, BusinessConstants::MAX_RETURN_SIZE);
        $list = [];
        foreach ($res as $v) {
            $list[] = [
                'id' => intval($v->id),
                'name' => $v->brand_name
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function getVehicleListAction()
    {
        $brandId = $this->getParameter('brand_id');

        $res = ConfigVehicleModel::getInstance()->getListByConditions(['brand_id' => $brandId], 0, BusinessConstants::MAX_RETURN_SIZE);

        $list = [];

        foreach ($res as $v) {
            $list[] = [
                'id' => $v->id,
                'name' => $v->vehicle_name
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function getVehicleModelListAction()
    {
        $brandId = $this->getParameter('brand_id');
        $modelId = $this->getParameter('model_id');

        $res = ConfigModel::getInstance()->getListByConditions(
            ['brand_id' => $brandId, 'vehicle_model_id' => $modelId],
            0,
            BusinessConstants::MAX_RETURN_SIZE,
            'year',
            'ASC'
        );
        $list = [];

        foreach ($res as $v) {
            $list[] = [
                'id' => $v->id,
                'name' => $v->year . '款 ' . $v->model_name,
                'year' => $v->year
            ];
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }

    public function importBrandAction()
    {
        if (!$this->request->hasFile('file') || !$this->request->file('file')->isValid()) {
            static::errorThrow('请上传Excel文档');
        }
        /**
         * @var $file \Illuminate\Http\UploadedFile
         */
        $file = $this->request->file('file');


        $excel = IOFactory::load($file->path());

        $data = $excel->getSheet(0)->toArray();

        if (count($data) < 2) {
            static::errorThrow('当前Excel文件内容为空');
        }

        // 检查列数
        if (count($data[0]) !== 1) {
            static::errorThrow('Excel文档格式不正确');
        }

        $allBrands = ConfigBrand::getInstance()->getAllListByOnField([], 'brand_name');
        array_shift($data);


        $values = [];
        foreach ($data as $v) {
            $brandName = $v[0];

            if (in_array($brandName, $allBrands)) {
                continue;
            }

            $values[] = [
                'brand_name' => $brandName,
                'pinyin' => strtoupper(substr(\Pinyin::abbr($brandName), 0, 1))
            ];
        }

        if ($values && !ConfigBrand::getInstance()->batchInsertByFields($values)) {
            static::errorThrow('写入数据失败请重试');
        }

        parent::renderSuccessJson();
    }
}