<?php
/**
 *
 * 日志设置
 *
 * @package   NiPHP
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */

use think\facade\Env;
use app\library\Base64;

return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'        => 'File',
    // 日志保存目录
    'path'        => app()->getRuntimePath() . 'log' . Base64::flag(),
    // 日志记录级别
    'level'       => [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'debug',
        'sql',
        // 'info',
    ],
    // 单文件日志写入
    'single'      => false,
    // 独立日志级别
    'apart_level' => [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'debug',
        'sql',
        'info',
    ],
    // 最大日志文件数量
    'max_files'   => 20,
    // 是否关闭日志写入
    'close'       => false,
];
