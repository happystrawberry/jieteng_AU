<?php
/**
 * 这个文件的作用
 *
 * @since   2018/12/1 11:55
 */


namespace App\Models;


class Setting extends BaseModel
{
    protected static $_TABLE = 'op_setting';

    public function updateValByKey($key, $val)
    {
        if ($this->getOneByField('skey', $key)) {
            $this->updateByFields(['sval' => $val, 'dateline' => time()], ['skey' => $key]);
        } else {
            $this->insertByFields(['skey' => $key, 'sval' => $val, 'dateline' => time()]);
        }
    }
}