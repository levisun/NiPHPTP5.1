<?php
/**
 *
 * 缓存配置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // 驱动方式
    'type'         => 'File',
    // 缓存前缀
    'prefix'       => NP_PREFIX,
    // 缓存有效期 0表示永久缓存
    'expire'       => 1200,
    'cache_subdir' => false,
];
