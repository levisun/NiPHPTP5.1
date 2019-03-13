DROP TABLE IF EXISTS `np_config`;
CREATE TABLE IF NOT EXISTS `np_config` (
  `id` smallint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(500) NOT NULL DEFAULT '' COMMENT '值',
  `lang` varchar(10) NOT NULL DEFAULT '' COMMENT '语言 niphp为全局设置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `value` (`value`) USING BTREE,
  KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='设置表';
