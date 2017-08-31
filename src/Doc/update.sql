# No.201708312330
# 修改管理员表
ALTER TABLE `s_user`
	CHANGE COLUMN `gender` `gender` TINYINT UNSIGNED NULL DEFAULT '1' COMMENT '性别：1#男性 2#女性' AFTER `truename`,
	CHANGE COLUMN `is_del` `is_del` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '逻辑删除：0#否 1#是' AFTER `status`,
	ADD COLUMN `is_super` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '是否超级管理员: 0#否 1#是' AFTER `is_del`;
