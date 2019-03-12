DROP TABLE IF EXISTS `np_session`;
CREATE TABLE IF NOT EXISTS `np_session` (
  `session_id` varchar(40) NOT NULL,
  `data` text NOT NULL COMMENT '内容',
  `update_time` varchar(80) NOT NULL DEFAULT '' COMMENT '刷新时间',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='session';
