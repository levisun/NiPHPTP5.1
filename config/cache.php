<?php
/**
 *
 * 缓存设置
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
    // 驱动方式
    'type'   => 'File',
    // 缓存保存目录
    'path'   => '',
    // 缓存前缀
    'prefix' => substr(sha1(__DIR__ . Request::rootDomain() . date('Ym')), 0, 7),
    // 缓存有效期 0表示永久缓存
    'expire' => 1200,
];
