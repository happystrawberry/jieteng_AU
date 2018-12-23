<?php
/**
 *
 *
 * @since   2018/5/31 21:33
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\Items;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ItemsController extends ApiBaseController
{
    public function saveAction()
    {
        $id = $this->getParameter('id', 0);
        $opNumber = $this->getParameter('op_number');
        $opType = $this->getIntegerParameter('op_type', 0);
        $itemName = $this->getParameter('item_name');
        $a0Times = $this->getParameter('a0_times', 0);
        $aTimes = $this->getParameter('a_times', 0);
        $bTimes = $this->getParameter('b_times', 0);
        $cTimes = $this->getParameter('c_times', 0);
        $dTimes = $this->getParameter('d_times', 0);
        $a0Cost = $this->getParameter('a0_cost', 0);
        $aCost = $this->getParameter('a_cost', 0);
        $bCost = $this->getParameter('b_cost', 0);
        $cCost = $this->getParameter('c_cost', 0);
        $dCost = $this->getParameter('d_cost', 0);

        if (($ret = Items::getInstance()->getOneByFields(['op_number' => $opNumber])) && (!$id || ($ret->id != $id))) {
            static::errorThrow('存在重复的项目编码，请重新填写!');
        }

        $values = [
            'op_number' => $opNumber,
            'op_type' => $opType,
            'item_name' => $itemName,
            'a0_times' => $a0Times,
            'a0_cost' => $a0Cost,
            'a_times' => $aTimes,
            'a_cost' => $aCost,
            'b_times' => $bTimes,
            'b_cost' => $bCost,
            'c_times' => $cTimes,
            'c_cost' => $cCost,
            'd_times' => $dTimes,
            'd_cost' => $dCost,
            'dateline' => time()
        ];

        if ($id) {
            Items::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            Items::getInstance()->insertByFields($values);
        }

        parent::renderSuccessJson();
    }

    public function disableAction()
    {
        $id = $this->getIntegerParameter('id');

        if (!Items::getInstance()->updateByFields(['enable' => 0], ['id' => $id, 'enable' => 1])) {
            static::errorThrow('操作失败');
        }

        parent::renderSuccessJson();
    }

    public function enableAction()
    {
        $id = $this->getParameter('id');

        if (!Items::getInstance()->updateByFields(['enable' => 1], ['id' => $id, 'enable' => 0])) {
            static::errorThrow('操作失败');
        }

        parent::renderSuccessJson();
    }

    public function importAction()
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
        if (count($data[0]) !== 13) {
            static::errorThrow('Excel文档格式不正确');
        }

        // 获取数据库字段名
        $tableFields = Items::getInstance()->getAllFields();
        array_shift($tableFields);
        $nameFields = $data[0];

        $opNumbers = [];
        $data = array_slice($data, 1);
        $values = [];
        foreach ($data as $v) {
            $value = [];
            foreach ($v as $k => $val) {
                $val = trim($val);
                if (empty($val)) {
                    static::errorThrow($nameFields[$k] . '内容为空,请检查');
                }

                if ($k == 1) {
                    if (!in_array($val, BusinessConstants::OP_ITEMS)) {
                        static::errorThrow('业务类别选择错误');
                    }

                    $value[$tableFields[$k]] = array_search($val, BusinessConstants::OP_ITEMS);
                } else {
                    $value[$tableFields[$k]] = $val;
                }
            }

            $opNumbers[] = $v[0];
            $value['enable'] = 1;
            $value['dateline'] = time();
            $values[$v[0]] = $value;
        }

        $existNumbers = Items::getInstance()->getExistListByOpNumbers($opNumbers);
        if ($existNumbers) {
            Items::getInstance()->deleteByOpNumbers($existNumbers);
        }

        if ($values && !Items::getInstance()->insert($values)) {
            static::errorThrow('写入数据失败请重试');
        }

        parent::renderSuccessJson();

    }

    public function listAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $name = $this->getParameter('name', '');
        $opType = $this->getIntegerParameter('op_type', 0);
        $conditions = [];
        if ($name) {
            $conditions['%item_name%'] = $name;
        }
        if ($opType) {
            $conditions['op_type'] = $opType;
        }

        $total = Items::getInstance()->countListByConditions($conditions);
        $list = [];
        if ($total) {
            $list = Items::getInstance()->getListByConditions($conditions, $pageOffset, $pageSize);
            foreach ($list as &$v) {
                $v->type_name = isset(BusinessConstants::OP_ITEMS[$v->op_type]) ? BusinessConstants::OP_ITEMS[$v->op_type] : '';
            }
            unset($v);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function getItemListAction()
    {
        $opType = $this->getIntegerParameter('op_type');
        $query = $this->getParameter('query', '');
        $class = strtolower($this->getParameter('class'));

        $list = Items::getInstance()->searchItems($opType, $query, $class);

        foreach ($list as &$v) {
            $times = $class . '_times';
            $v->tiems = $v->$times;
            unset($v->$times);
            $cost = $class . '_cost';
            $v->cost = $v->$cost;
            unset($v->$cost);
        }

        parent::renderSuccessJson([
            'list' => $list
        ]);
    }
}