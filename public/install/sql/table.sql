DROP TABLE IF EXISTS `np_article_data`;
CREATE TABLE IF NOT EXISTS `np_article_data` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `main_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  `fields_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `data` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章扩展表';


DROP TABLE IF EXISTS `np_feedback`;
CREATE TABLE IF NOT EXISTS `np_feedback` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '作者名',
  `content` varchar(300) NOT NULL DEFAULT '' COMMENT '内容',
  `category_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `mebmer_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `is_pass` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '评论IP',
  `ip_attr` varchar(100) NOT NULL DEFAULT '' COMMENT '评论IP地区',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`),
  KEY `is_pass` (`is_pass`),
  KEY `delete_time` (`delete_time`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='反馈表';

-- --------------------------------------------------------

--
-- 表的结构 `np_feedback_data`
--

DROP TABLE IF EXISTS `np_feedback_data`;
CREATE TABLE IF NOT EXISTS `np_feedback_data` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `main_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '反馈ID',
  `fields_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `data` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='反馈扩展表';

-- --------------------------------------------------------

--
-- 表的结构 `np_fields`
--

DROP TABLE IF EXISTS `np_fields`;
CREATE TABLE IF NOT EXISTS `np_fields` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '字段名',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  `is_require` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '必填',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自定义字段表';

-- --------------------------------------------------------

--
-- 表的结构 `np_fields_type`
--

DROP TABLE IF EXISTS `np_fields_type`;
CREATE TABLE IF NOT EXISTS `np_fields_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '类型名',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  `regex` varchar(100) NOT NULL DEFAULT '' COMMENT '验证方式',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='字段类型表';

--
-- 转存表中的数据 `np_fields_type`
--

INSERT INTO `np_fields_type` (`id`, `name`, `description`, `regex`) VALUES
(1, 'text', '文本', 'require'),
(2, 'number', '数字', 'number'),
(3, 'email', '邮箱', 'email'),
(4, 'url', 'URL地址', 'url'),
(5, 'currency', '货币', 'currency'),
(6, 'abc', '字母', '/^[A-Za-z]+$/'),
(7, 'idcards', '身份证', '/^(d{14}|d{17})(d|[xX])$/'),
(8, 'phone', '移动电话', '/^(1)[1-9][0-9]{9}$/'),
(9, 'landline', '固话', '/^d{3,4}-d{7,8}(-d{3,4})?$/'),
(10, 'age', '年龄', '/^[1-9][0-9]?[0-9]?$/'),
(11, 'date', '日期', '/^d{4}(-|/|.)d{1,2}1d{1,2}$/');

-- --------------------------------------------------------

--
-- 表的结构 `np_ipinfo`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_level`
--




-- --------------------------------------------------------

--
-- 表的结构 `np_message`
--

DROP TABLE IF EXISTS `np_message`;
CREATE TABLE IF NOT EXISTS `np_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '作者名',
  `content` varchar(300) NOT NULL DEFAULT '' COMMENT '内容',
  `reply` varchar(300) NOT NULL DEFAULT '' COMMENT '回复',
  `category_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `mebmer_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `is_pass` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '评论IP',
  `ip_attr` varchar(100) NOT NULL DEFAULT '' COMMENT '评论IP地区',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`),
  KEY `is_pass` (`is_pass`),
  KEY `delete_time` (`delete_time`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='留言表';

-- --------------------------------------------------------

--
-- 表的结构 `np_message_data`
--

DROP TABLE IF EXISTS `np_message_data`;
CREATE TABLE IF NOT EXISTS `np_message_data` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `main_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '留言ID',
  `fields_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `data` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='留言扩展表';



-- --------------------------------------------------------

--
-- 表的结构 `np_type`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_visit`
--


