-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        10.1.19-MariaDB - mariadb.org binary distribution
-- 服务器操作系统:                      Win32
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 servepro.s_limit 结构
CREATE TABLE IF NOT EXISTS `s_limit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理者id',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT '父级权限id',
  `name` varchar(50) NOT NULL COMMENT '权限名称',
  `node` varchar(50) NOT NULL COMMENT '对应权限',
  `rank` int(10) unsigned DEFAULT '0' COMMENT '排序权重，数值越大权重越高，显示越靠前',
  `status` int(10) unsigned DEFAULT '1' COMMENT '是否启用：0#禁用 1#启用',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='后台管理系统_权限表（颗粒度到动作，即action）\r\n分为多级处理，顶层应为对应controller，子级应为对应action';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_menu 结构
CREATE TABLE IF NOT EXISTS `s_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  `name` varchar(50) NOT NULL COMMENT '菜单项名称',
  `node` varchar(255) DEFAULT NULL COMMENT '节点对应的页面url',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级节点id',
  `class` varchar(255) DEFAULT NULL COMMENT '节点样式',
  `rank` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重，数值越大权重越高，也就越靠前',
  `status` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '启动状态：0#禁用 1#启动',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_time` datetime DEFAULT NULL COMMENT '创建日期时间',
  `update_time` datetime DEFAULT NULL COMMENT '更改日期时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_菜单表';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_menu_limit 结构
CREATE TABLE IF NOT EXISTS `s_menu_limit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应菜单id',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应角色id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_菜单_角色管理表（控制菜单是否对某角色可见）';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_role 结构
CREATE TABLE IF NOT EXISTS `s_role` (
  `id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL COMMENT '创建者id',
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `rank` int(10) unsigned zerofill NOT NULL COMMENT '排序权重，数值越大权重越高（显示越靠前）',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更改时间',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_角色表';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_role_limit 结构
CREATE TABLE IF NOT EXISTS `s_role_limit` (
  `id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理者id',
  `role_id` int(10) unsigned NOT NULL COMMENT '对应角色id',
  `limit_id` int(10) unsigned NOT NULL COMMENT '对应权限id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_角色_权限表';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_user 结构
CREATE TABLE IF NOT EXISTS `s_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `account` varchar(50) NOT NULL COMMENT '用户账号',
  `password` varchar(50) NOT NULL COMMENT '用户密码',
  `nick` varchar(50) NOT NULL COMMENT '用户昵称',
  `truename` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `gender` int(10) unsigned DEFAULT '1' COMMENT '性别：1#男性 2#女性',
  `status` int(10) unsigned DEFAULT '1' COMMENT '状态：0#禁用 1#启用',
  `is_del` int(10) unsigned DEFAULT '0' COMMENT '逻辑删除：0#否 1#是',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_用户管理表';

-- 数据导出被取消选择。
-- 导出  表 servepro.s_user_role 结构
CREATE TABLE IF NOT EXISTS `s_user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  `user_id` int(10) unsigned NOT NULL COMMENT '对应用户id',
  `role_id` int(10) unsigned NOT NULL COMMENT '对应角色id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理系统_用户_角色';

-- 数据导出被取消选择。
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
