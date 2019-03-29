<?php
/**
 *
 * 会话设置
 *
 * @package   NiPHP
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */

use think\facade\Request;

return [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // 驱动方式 支持redis memcache memcached
    'type'           => 'app\library\Session',
    // 是否自动开启 SESSION
    'auto_start'     => false,
    // Session配置参数
    'options'        => [
    ],
];
