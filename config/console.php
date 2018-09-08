<?php
/**
 *
 * 控制台配置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

return [
    'name'      => 'Think Console',
    'version'   => '0.1',
    'user'      => null,
    'auto_path' => env('app_path') . 'command' . DIRECTORY_SEPARATOR,
];
