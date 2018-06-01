<?php
/**
 *
 * 日志设置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'  => 'File',
    // 日志保存目录
    'path'  => '',
    // 日志记录级别
    'level' => [],
    // 'json'  => true,
    'apart_level' => [
        'error',
        'notice',
        'sql',
    ],
];
