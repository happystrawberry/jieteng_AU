<?php
/**
 *
 *
 * @since   2018/6/5 20:50
 */


namespace App\Models;


use App\Constants\BusinessConstants;

class BUserVehicle extends BaseModel
{
    protected static $_TABLE = 'user_vehicle';

    protected static $_conn = 'mysql2';

    public function getUserVehicleInfoByUids($uids)
    {
        if (empty($uids)) {
            return [];
        }

        $data = [];
        $list = $this->getListByConditions(
            ['uid' => $uids], 0, BusinessConstants::MAX_RETURN_SIZE, 'uid', 'desc', ['license_plate', 'uid', 'model']);

        foreach ($list as $v) {
            $data[$v->uid] = $v;
        }

        return $data;
    }
}