升级bishang数据库SQL
ALTER TABLE `config_model` 
	ADD COLUMN `model_class` varchar(6)  COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '车辆等级' after `year` , COLLATE ='utf8mb4_bin' ;