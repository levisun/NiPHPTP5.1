DROP TABLE IF EXISTS `np_model`;
CREATE TABLE IF NOT EXISTS `np_model` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模型名',
  `table_name` varchar(20) NOT NULL DEFAULT '' COMMENT '表名',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模型表';

INSERT INTO `np_model` (`id`, `name`, `table_name`, `remark`, `status`, `sort`) VALUES
(1, 'article', 'article', '文章模型', 1, 8),
(2, 'picture', 'picture', '图片模型', 1, 7),
(3, 'download', 'download', '下载模型', 1, 6),
(4, 'page', 'page', '单页模型', 1, 5),
(5, 'feedback', 'feedback', '反馈模型', 1, 4),
(6, 'message', 'message', '留言模型', 1, 3),
(7, 'link', 'link', '友链模型', 1, 2),
(8, 'external', 'external', '外部模型', 1, 1);
