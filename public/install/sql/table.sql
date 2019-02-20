DROP TABLE IF EXISTS `np_access`;
CREATE TABLE IF NOT EXISTS `np_access` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '组ID',
  `node_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '节点ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '节点等级',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '节点名',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `node_id` (`node_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

DROP TABLE IF EXISTS `np_admin`;
CREATE TABLE IF NOT EXISTS `np_admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '佐料',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `last_login_ip_attr` varchar(255) NOT NULL DEFAULT '' COMMENT '登录IP地区',
  `last_login_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `password` (`password`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

DROP TABLE IF EXISTS `np_config`;
CREATE TABLE IF NOT EXISTS `np_config` (
  `id` smallint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(500) NOT NULL DEFAULT '' COMMENT '值',
  `lang` varchar(20) NOT NULL DEFAULT '' COMMENT '语言 niphp为全局设置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `value` (`value`(191)),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='设置表';

DROP TABLE IF EXISTS `np_ipinfo`;
CREATE TABLE IF NOT EXISTS `np_ipinfo` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `country_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '国家',
  `province_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '省',
  `city_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '市',
  `area_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '区',
  `isp` varchar(20) NOT NULL DEFAULT '' COMMENT '运营商',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `update_time` (`update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='IP地域信息';

DROP TABLE IF EXISTS `np_node`;
CREATE TABLE IF NOT EXISTS `np_node` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `level` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '节点操作名',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '节点说明',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `sort` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `level` (`level`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='节点表';

DROP TABLE IF EXISTS `np_region`;
CREATE TABLE IF NOT EXISTS `np_region` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区表';

DROP TABLE IF EXISTS `np_role`;
CREATE TABLE IF NOT EXISTS `np_role` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '组名',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='组表';

DROP TABLE IF EXISTS `np_role_admin`;
CREATE TABLE IF NOT EXISTS `np_role_admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `role_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '组ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员组关系表';

DROP TABLE IF EXISTS `np_searchengine`;
CREATE TABLE IF NOT EXISTS `np_searchengine` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '搜索引擎名',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '访问agent',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '统计数量',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='搜索引擎';

DROP TABLE IF EXISTS `np_session`;
CREATE TABLE IF NOT EXISTS `np_session` (
  `session_id` varchar(40) NOT NULL,
  `data` text NOT NULL COMMENT '内容',
  `update_time` varchar(80) NOT NULL DEFAULT '' COMMENT '刷新时间',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='session';

DROP TABLE IF EXISTS `np_visit`;
CREATE TABLE IF NOT EXISTS `np_visit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '访问IP',
  `ip_attr` varchar(255) NOT NULL DEFAULT '' COMMENT '访问IP地区',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '访问agent',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '统计数量',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访问表';
