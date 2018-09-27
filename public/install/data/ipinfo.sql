DROP TABLE IF EXISTS `np_ipinfo`;
CREATE TABLE IF NOT EXISTS `np_ipinfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `country_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `update_time` (`update_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT 'IP地域信息';

INSERT INTO `np_ipinfo` (`ip`, `country_id`, `province_id`, `city_id`, `area_id`, `update_time`, `create_time`) VALUES
('117.22.144.218', 100000, 610000, 220403, 0, 1534605995, 1534604292),
('::1', 0, 0, 0, 0, 1534606876, 1534606876);
