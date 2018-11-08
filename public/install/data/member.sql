DROP TABLE IF EXISTS `np_member`;
CREATE TABLE IF NOT EXISTS `np_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `portrait` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别',
  `birthday` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
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
  KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `password` (`password`),
  KEY `gender` (`gender`),
  KEY `birthday` (`birthday`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '会员';

DROP TABLE IF EXISTS `np_member_oauth`;
CREATE TABLE IF NOT EXISTS `np_member_oauth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT 'openid',
  `nick` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `openid` (`openid`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '第三方登录会员';

DROP TABLE IF EXISTS `np_member_wechat`;
CREATE TABLE IF NOT EXISTS `np_member_wechat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
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
  KEY `user_id` (`user_id`),
  KEY `appid` (`appid`),
  KEY `appname` (`appname`),
  UNIQUE KEY `openid` (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT '微信用户信息表';

DROP TABLE IF EXISTS `np_level_member`;
CREATE TABLE IF NOT EXISTS `np_level_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `level_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '组ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '会员组关系表';

DROP TABLE IF EXISTS `np_level`;
CREATE TABLE IF NOT EXISTS `np_level` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '组名',
  `integral` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `integral` (`integral`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '会员组';
INSERT INTO np_level(`name`, `status`, `integral`) VALUES
('钻石会员', 1, 500000000),
('黄金会员', 1, 30000000),
('白金会员', 1, 500000),
('VIP会员', 1, 3000),
('高级会员', 1, 500),
('普通会员', 1, 0);
