<?php
/**
 *
 *
 * @since   2018/6/2 9:26
 */


namespace App\Models;


class ConfigBrand extends BaseModel
{
    protected static $_TABLE = 'config_brand';

    protected static $_conn = 'mysql2';

    public function getAllBrandNames()
    {
        $res = $this->getAllListByFields([], ['id', 'brand_name']);

        $data = [];
        foreach ($res as $v) {
            $data[$v->id] = $v->brand_name;
        }

        return $data;
    }
}