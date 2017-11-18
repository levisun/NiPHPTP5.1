<?php
/**
 *
 * 缓存设置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: cache.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */

return [
    // 驱动方式
    'type'   => 'File',
    // 缓存前缀
    'prefix' => 'np_insomnia_',
    // 缓存有效期 0表示永久缓存
    'expire' => 1200,
    // 'cache_subdir' => false,
];
