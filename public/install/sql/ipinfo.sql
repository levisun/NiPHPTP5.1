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
  KEY `update_time` (`update_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='IP地域信息';
