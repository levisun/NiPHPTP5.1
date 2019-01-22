<?php
/**
 *
 * Cookie配置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // cookie 名称前缀
    'prefix'    => strtoupper(substr(md5(__DIR__), -3)) . '_',
    // cookie 保存时间
    'expire'    => 0,
    // cookie 保存路径
    'path'      => '/',
    // cookie 有效域名
    'domain'    => '.' . request()->rootDomain(),
    //  cookie 启用安全传输
    'secure'    => false,
    // httponly设置
    'httponly'  => '',
    // 是否使用 setcookie
    'setcookie' => true,
];
