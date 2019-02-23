-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2019-02-21 04:50:42
-- 服务器版本： 10.1.37-MariaDB
-- PHP 版本： 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `niphp`
--

-- --------------------------------------------------------

--
-- 表的结构 `np_access`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_admin`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_category`
--

DROP TABLE IF EXISTS `np_category`;
CREATE TABLE IF NOT EXISTS `np_category` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '栏目名',
  `aliases` varchar(20) NOT NULL DEFAULT '' COMMENT '别名',
  `seo_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` varchar(300) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `type_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型ID',
  `model_id` smallint(6) UNSIGNED NOT NULL COMMENT '模型ID',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示',
  `is_channel` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '频道页',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `access_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权限',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '外链地址',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lang` varchar(10) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `aliases` (`aliases`),
  KEY `pid` (`pid`),
  KEY `type_id` (`type_id`),
  KEY `model_id` (`model_id`),
  KEY `is_show` (`is_show`),
  KEY `is_channel` (`is_channel`),
  KEY `sort` (`sort`),
  KEY `access_id` (`access_id`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='栏目表';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='设置表';

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
-- 表的结构 `np_model`
--

DROP TABLE IF EXISTS `np_model`;
CREATE TABLE IF NOT EXISTS `np_model` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模型名',
  `table_name` varchar(20) NOT NULL DEFAULT '' COMMENT '表名',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `table_name` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模型表';

-- --------------------------------------------------------

--
-- 表的结构 `np_node`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_region`
--

DROP TABLE IF EXISTS `np_region`;
CREATE TABLE IF NOT EXISTS `np_region` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区表';

-- --------------------------------------------------------

--
-- 表的结构 `np_role`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_role_admin`
--

DROP TABLE IF EXISTS `np_role_admin`;
CREATE TABLE IF NOT EXISTS `np_role_admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `role_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '组ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员组关系表';

-- --------------------------------------------------------

--
-- 表的结构 `np_searchengine`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `np_session`
--

DROP TABLE IF EXISTS `np_session`;
CREATE TABLE IF NOT EXISTS `np_session` (
  `session_id` varchar(40) NOT NULL,
  `data` text NOT NULL COMMENT '内容',
  `update_time` varchar(80) NOT NULL DEFAULT '' COMMENT '刷新时间',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='session';

-- --------------------------------------------------------

--
-- 表的结构 `np_visit`
--

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
COMMIT;
