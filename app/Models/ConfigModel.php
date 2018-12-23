<?php
/**
 *
 *
 * @since   2018/6/2 9:27
 */


namespace App\Models;


use App\Constants\CacheTimeoutConstants;

class ConfigModel extends BaseModel
{
    protected static $_TABLE = 'config_model';

    protected static $_conn = 'mysql2';

    public function getVehicleModelName($id)
    {

        $res = $this->getModelCache($id);

        if (is_array($res)) {
            return $res;
        }

        $SQL = <<<EOF
SELECT
  cb.brand_name,
  cvm.vehicle_name,
  cm.model_name,
  cm.year
FROM
  config_model cm
  LEFT JOIN config_vehicle_model cvm
    ON cm.`vehicle_model_id`=cvm.`id`
  LEFT JOIN config_brand cb
    ON cb.`id`= cm.brand_id AND cvm.`brand_id`=cb.`id`
WHERE cm.id = ?
EOF;

        $res = (array)$this->selectOneByRawSql($SQL, [$id]);
        $res = implode('-', $res) . ' 款';
        $this->setModelCache($res, CacheTimeoutConstants::LOCAL_CACHE, $id);

        return $res;
    }

    public function getList($query, $offset, $size)
    {
        $where = '';
        if ($query) {
            $where = <<<EOF
 WHERE (cb.brand_name LIKE CONCAT('%',?,'%') OR cvm.`vehicle_name` LIKE CONCAT('%',?,'%') OR cm.`model_name` LIKE CONCAT('%',?,'%')) 
EOF;

        }

        $sql = <<<EOF
SELECT
  cm.*,
  cvm.`vehicle_name`,
  cb.`brand_name`
FROM
  config_model cm
  LEFT JOIN config_vehicle_model cvm
    ON cvm.`id` = cm.`vehicle_model_id`
  LEFT JOIN config_brand cb
    ON cb.`id` = cvm.`brand_id`
    AND cm.`brand_id` = cb.`id` {$where}  ORDER BY cm.id DESC LIMIT {$offset},{$size}
EOF;

        return $this->selectByRawSql($sql, $query ? [$query, $query, $query] : []);
    }

    public function countList($query)
    {
        $where = '';
        if ($query) {
            $where = <<<EOF
 WHERE (cb.brand_name LIKE CONCAT('%',?,'%') OR cvm.`vehicle_name` LIKE CONCAT('%',?,'%') OR cm.`model_name` LIKE CONCAT('%',?,'%')) 
EOF;

        }

        $sql = <<<EOF
SELECT
 COUNT(*) AS num
FROM
  config_model cm
  LEFT JOIN config_vehicle_model cvm
    ON cvm.`id` = cm.`vehicle_model_id`
  LEFT JOIN config_brand cb
    ON cb.`id` = cvm.`brand_id`
    AND cm.`brand_id` = cb.`id` {$where}
EOF;

        return $this->selectOneByRawSql($sql, $query ? [$query, $query, $query] : [])->num;
    }
}