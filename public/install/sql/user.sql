DROP TABLE IF EXISTS `np_user`;
CREATE TABLE IF NOT EXISTS `np_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `portrait` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别',
  `birthday` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `level_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '等级ID',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '电话',
  `salt` char(6) NOT NULL COMMENT '佐料',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `last_login_ip_attr` varchar(255) NOT NULL DEFAULT '' COMMENT '登录IP地区',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `password` (`password`) USING BTREE,
  KEY `gender` (`gender`) USING BTREE,
  KEY `birthday` (`birthday`) USING BTREE,
  KEY `level_id` (`level_id`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `city_id` (`city_id`) USING BTREE,
  KEY `area_id` (`area_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '用户';



DROP TABLE IF EXISTS `np_user_oauth`;
CREATE TABLE IF NOT EXISTS `np_user_oauth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT 'openid',
  `nick` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE,
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '第三方登录用户';



DROP TABLE IF EXISTS `np_user_wechat`;
CREATE TABLE IF NOT EXISTS `np_user_wechat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `appid` varchar(32) NOT NULL DEFAULT '' COMMENT 'APPID',
  `appname` varchar(32) NOT NULL DEFAULT '' COMMENT 'APP NAME'
  `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '关注状态',
  `openid` varchar(32) NOT NULL DEFAULT '' COMMENT '用户标识',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 1男 2女 0未知',
  `city` varchar(10) NOT NULL DEFAULT '' COMMENT '城市',
  `country` varchar(10) NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(10) NOT NULL DEFAULT '' COMMENT '省份',
  `language` varchar(10) NOT NULL DEFAULT '' COMMENT '语言',
  `avatar_url` varchar(500) NOT NULL DEFAULT '' COMMENT '头像',
  `subscribe_time` int(11) NOT NULL DEFAULT '' COMMENT '关注时间',
  `unionid` varchar(32) NOT NULL DEFAULT '' COMMENT '',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `groupid` varchar(50) NOT NULL DEFAULT '' COMMENT '分组ID',
  `tagid_list` varchar(500) NOT NULL DEFAULT '' COMMENT '标签ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `appid` (`appid`) USING BTREE,
  KEY `appname` (`appname`) USING BTREE,
  UNIQUE KEY `openid` (`openid`),
  KEY `unionid` (`unionid`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT '微信用户信息表';



DROP TABLE IF EXISTS `np_level`;
CREATE TABLE IF NOT EXISTS `np_level` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '组名',
  `credit` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '积分',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `credit` (`credit`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户组';
INSERT INTO `np_level` (`id`, `name`, `credit`, `status`, `remark`) VALUES
(1, '钻石会员', 500000000, 1, ''),
(2, '黄金会员', 30000000, 1, ''),
(3, '白金会员', 500000, 1, ''),
(4, 'VIP会员', 3000, 1, ''),
(5, '高级会员', 500, 1, ''),
(6, '普通会员', 0, 1, '');
