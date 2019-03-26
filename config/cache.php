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

use app\library\Base64;

return [
    // 驱动方式
    'type'          => 'File',
    // 缓存保存目录
    'path'          => app()->getRuntimePath() . 'cache' . Base64::flag(),
    // 缓存前缀
    'prefix'        => '',
    // 缓存有效期 0表示永久缓存
    'expire'        => APP_DEBUG ? 1440: 14400,
    // 关闭子目录
    'cache_subdir'  => false,
    // 开启转义
    'serialize'     => true,
    // 开启压缩
    'data_compress' => false,
];
