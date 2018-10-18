<?php
/**
 *
 * 行为
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */

// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [
        // 并发抛出500[每次访问万分之一几率执行操作]
        // 'app\\common\\behavior\\Concurrent',
        // GET请求下生成API请求TOKEN
        'app\\common\\behavior\\CreateApiToken',
    ],
    // 应用开始
    'app_begin'    => [],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [
        // 访问记录
        'app\\common\\behavior\\Visit',
        // GET请求下清除运行垃圾文件[每次访问百分之一几率执行操作]
        'app\\common\\behavior\\RemoveRunGarbage'
    ]
];
