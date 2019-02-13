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

use app\common\library\Base64;

return [
    // 驱动方式
    'type'   => 'File',
    // 缓存保存目录
    'path'   => '',
    // 缓存前缀
    'prefix' => Base64::flag(date('Ym')),
    // 缓存有效期 0表示永久缓存
    'expire' => 1200,
];
