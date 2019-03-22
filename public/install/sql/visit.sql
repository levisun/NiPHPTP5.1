DROP TABLE IF EXISTS `np_searchengine`;
CREATE TABLE IF NOT EXISTS `np_searchengine` (
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '搜索引擎名',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '访问agent',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '统计数量',
  KEY `date` (`date`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='搜索引擎';



DROP TABLE IF EXISTS `np_visit`;
CREATE TABLE IF NOT EXISTS `np_visit` (
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '访问IP',
  `ip_attr` varchar(100) NOT NULL DEFAULT '' COMMENT '访问IP地区',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '访问agent',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '统计数量',
  KEY `date` (`date`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访问表';
