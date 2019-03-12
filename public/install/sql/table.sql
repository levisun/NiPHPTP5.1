-- --------------------------------------------------------

--
-- 表的结构 `np_access`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_admin`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_article`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_article_data`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_category`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_config`
--

DROP TABLE IF EXISTS `np_config`;
CREATE TABLE IF NOT EXISTS `np_config` (
  `id` smallint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(500) NOT NULL DEFAULT '' COMMENT '值',
  `lang` varchar(10) NOT NULL DEFAULT '' COMMENT '语言 niphp为全局设置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `value` (`value`(191)),
  KEY `lang` (`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='设置表';

--
-- 转存表中的数据 `np_config`
--

INSERT INTO `np_config` (`id`, `name`, `value`, `lang`) VALUES
(1, 'index_theme', 'default', 'zh-cn'),
(2, 'index_script', '', 'zh-cn'),
(3, 'index_bottom', '&lt;a href=&quot;http://www.miitbeian.gov.cn&quot; target=&quot;_blank&quot;&gt;陕icp备15001502号-1&lt;/a&gt;', 'zh-cn'),
(4, 'index_copyright', 'copyright &amp;copy; 2014-2015 &lt;a href=&quot;http://www.niphp.com&quot; target=&quot;_blank&quot;&gt;niphp.com&lt;/a&gt;版权所有', 'zh-cn'),
(5, 'index_sitename', '腐朽的木屋', 'zh-cn'),
(6, 'index_keywords', 'php, javascript, js, html, css, thinkphp, tp', 'zh-cn'),
(7, 'index_description', '开发WEB应用时的笔记、问题和学习资料。', 'zh-cn');

-- --------------------------------------------------------

--
-- 表的结构 `np_feedback`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_level`
--



-- --------------------------------------------------------

--
-- 表的结构 `np_link`
--

DROP TABLE IF EXISTS `np_link`;
CREATE TABLE IF NOT EXISTS `np_link` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '标志',
  `description` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `category_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `is_pass` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `sort` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击量',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发布人ID',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '跳转链接',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='友链表';

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





DROP TABLE IF EXISTS `np_tags`;
CREATE TABLE IF NOT EXISTS `np_tags` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '标签名',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '标签文章数量',
  `lang` varchar(20) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `count` (`count`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签表';

-- --------------------------------------------------------

--
-- 表的结构 `np_tags_article`
--

DROP TABLE IF EXISTS `np_tags_article`;
CREATE TABLE IF NOT EXISTS `np_tags_article` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tags_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '标签ID',
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  PRIMARY KEY (`id`),
  KEY `tags_id` (`tags_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签文章关联表';

-- --------------------------------------------------------

--
-- 表的结构 `np_type`
--

DROP TABLE IF EXISTS `np_type`;
CREATE TABLE IF NOT EXISTS `np_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名',
  `description` varchar(555) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分类';

-- --------------------------------------------------------

--
-- 表的结构 `np_visit`
--


