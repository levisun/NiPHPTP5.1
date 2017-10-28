<?php
/**
 *
 * 数据库设置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: database.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'tp_new',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => '',
    // 端口
    'hostport'        => '',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'np_',
    // 数据库调试模式
    'debug'           => APP_DEBUG,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 是否需要进行SQL性能分析
    'sql_explain'     => APP_DEBUG,
];
