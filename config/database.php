<?php
/**
 *
 * 数据库配置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'tp_new',
    // 数据库用户名
    'username'        => 'root',
    // 数据库密码
    'password'        => '',
    // 数据库连接端口
    'hostport'        => '3306',
    // 数据库连接参数
    'params'          => [
        \PDO::ATTR_CASE                     => \PDO::CASE_NATURAL,              // 列名按照原始
        \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION,         // 错误静默模式
        \PDO::ATTR_ORACLE_NULLS             => \PDO::NULL_NATURAL,              // 不转换
        \PDO::ATTR_STRINGIFY_FETCHES        => false,
        \PDO::ATTR_EMULATE_PREPARES         => false,
        \PDO::ATTR_PERSISTENT               => true,                            // 长链接
        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,                            // 查询缓存
    ],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'np_',
    // 数据库调试模式
    'debug'           => APP_DEBUG,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => APP_DEBUG,
    // 查询对象
    'query'           => '\\think\\db\\Query',
];
