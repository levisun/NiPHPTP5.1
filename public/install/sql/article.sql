DROP TABLE IF EXISTS `np_model`;
CREATE TABLE IF NOT EXISTS `np_model` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模型名',
  `table_name` varchar(20) NOT NULL DEFAULT '' COMMENT '表名',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='模型表';

INSERT INTO `np_model` (`id`, `name`, `table_name`, `remark`, `status`, `sort_order`) VALUES
(1, 'article', 'article', '文章模型', 1, 8),
(2, 'picture', 'picture', '图片模型', 1, 7),
(3, 'download', 'download', '下载模型', 1, 6),
(4, 'page', 'page', '单页模型', 1, 5),
(5, 'feedback', 'feedback', '反馈模型', 1, 4),
(6, 'message', 'message', '留言模型', 1, 3),
(7, 'link', 'link', '友链模型', 1, 2),
(8, 'external', 'external', '外部模型', 1, 1);




DROP TABLE IF EXISTS `np_category`;
CREATE TABLE IF NOT EXISTS `np_category` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '栏目名',
  `aliases` varchar(20) NOT NULL DEFAULT '' COMMENT '别名',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `type_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `model_id` smallint(5) UNSIGNED NOT NULL COMMENT '模型ID',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示',
  `is_channel` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '频道页',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `access_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权限',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '外链地址',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `pid` (`pid`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `model_id` (`model_id`) USING BTREE,
  KEY `is_show` (`is_show`) USING BTREE,
  KEY `access_id` (`access_id`) USING BTREE,
  KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='栏目表';



DROP TABLE IF EXISTS `np_tags`;
CREATE TABLE IF NOT EXISTS `np_tags` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '标签名',
  `count` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '标签文章数量',
  `lang` varchar(20) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='标签表';

DROP TABLE IF EXISTS `np_tags_article`;
CREATE TABLE IF NOT EXISTS `np_tags_article` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tags_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '标签ID',
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  PRIMARY KEY (`id`),
  KEY `tags_id` (`tags_id`) USING BTREE,
  KEY `article_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='标签文章关联表';

DROP TABLE IF EXISTS `np_type`;
CREATE TABLE IF NOT EXISTS `np_type` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名',
  `remark` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='分类';



DROP TABLE IF EXISTS `np_fields_type`;
CREATE TABLE IF NOT EXISTS `np_fields_type` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '类型名',
  `regex` varchar(100) NOT NULL DEFAULT '' COMMENT '验证方式',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自定义字段表';
INSERT INTO `np_fields_type` (`id`, `name`, `remark`, `regex`) VALUES
(1, 'text', '文本', 'require'),
(2, 'number', '数字', 'number'),
(3, 'email', '邮箱', 'email'),
(4, 'url', 'URL地址', 'url'),
(5, 'currency', '货币', 'currency'),
(6, 'abc', '字母', '/^[A-Za-z]+$/'),
(7, 'idcards', '身份证', '/^(\d{14}|\d{17})(\d|[xX])$/'),
(8, 'phone', '移动电话', '/^(1)[1-9][0-9]{9}$/'),
(9, 'landline', '固话', '/^\d{3,4}-\d{7,8}(-\d{3,4})?$/'),
(10, 'age', '年龄', '/^[1-9][0-9]?[0-9]?$/'),
(11, 'date', '日期', '/^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/');

DROP TABLE IF EXISTS `np_fields`;
CREATE TABLE IF NOT EXISTS `np_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '字段名',
  `maxlength` smallint(5) NOT NULL DEFAULT '500' COMMENT '最大长度',
  `is_require` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '必填',
  `sort_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自定义字段表';


DROP TABLE IF EXISTS `np_article`;
CREATE TABLE IF NOT EXISTS `np_article` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `category_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发布人ID',
  `is_pass` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `is_com` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_top` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '置顶',
  `is_hot` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最热',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击量',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '作者名',
  `origin` varchar(200) NOT NULL DEFAULT '' COMMENT '来源',
  `show_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '显示时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `access_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '访问权限',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `title` (`title`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `is_pass` (`is_pass`) USING BTREE,
  KEY `is_com` (`is_com`) USING BTREE,
  KEY `is_top` (`is_top`) USING BTREE,
  KEY `is_hot` (`is_hot`) USING BTREE,
  KEY `show_time` (`show_time`) USING BTREE,
  KEY `delete_time` (`delete_time`) USING BTREE,
  KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4mb4 COMMENT='文章表';

DROP TABLE IF EXISTS `np_article_content`;
CREATE TABLE `np_article_content` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图',
  `content` longtext COMMENT '内容详情',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章内容表';

DROP TABLE IF EXISTS `np_article_file`;
CREATE TABLE `np_download_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `file_size` varchar(10) DEFAULT '' COMMENT '文件大小',
  `file_ext` varchar(50) DEFAULT '' COMMENT '文件后缀名',
  `file_name` varchar(100) DEFAULT '' COMMENT '文件名',
  `file_mime` varchar(50) DEFAULT '' COMMENT '文件类型',
  `uhash` varchar(200) DEFAULT '' COMMENT '自定义的一种加密方式，用于文件下载权限验证',
  `md5file` varchar(200) DEFAULT '' COMMENT 'md5_file加密，可以检测上传/下载的文件包是否损坏',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章下载附件表';

DROP TABLE IF EXISTS `np_article_image`;
CREATE TABLE `np_picture_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `image_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `width` smallint(5) DEFAULT '0' COMMENT '图片宽度',
  `height` smallint(5) DEFAULT '0' COMMENT '图片高度',
  `filesize` mediumint(8) unsigned DEFAULT '0' COMMENT '文件大小',
  `mime` varchar(50) DEFAULT '' COMMENT '图片类型',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章图集图片表';



DROP TABLE IF EXISTS `np_link`;
CREATE TABLE IF NOT EXISTS `np_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '标志',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `remark` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布人ID',
  `is_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核',
  `hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `is_pass` (`is_pass`) USING BTREE,
  KEY `delete_time` (`delete_time`) USING BTREE,
  KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='友情链接表';








DROP TABLE IF EXISTS `np_article_product_attr`;
CREATE TABLE `np_article_product_attr` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text COMMENT '属性值',
  `attr_price` varchar(255) DEFAULT '' COMMENT '属性价格',
  `create_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品表单属性值';



DROP TABLE IF EXISTS `ey_article_product_attribute`;
CREATE TABLE `ey_article_product_attribute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT '' COMMENT '属性名称',
  `typeid` int(11) unsigned DEFAULT '0' COMMENT '栏目id',
  `attr_index` tinyint(1) unsigned DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_input_type` tinyint(1) unsigned DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框',
  `attr_values` text COMMENT '可选值列表',
  `sort_order` int(11) unsigned DEFAULT '0' COMMENT '属性排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`typeid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品表单属性表';
