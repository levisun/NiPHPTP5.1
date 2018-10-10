<?php
/**
 *
 * 行为
 *
 * @package   NiPHPCMS
 * @category  application/cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */

// 应用行为扩展定义文件
return [
    // 模块初始化
    'module_init'  => [
        // 并发抛出500[每次访问万分之一几率执行操作]
        'app\\cms\\behavior\\Concurrent',
        // HTML静态文件
        'app\\cms\\behavior\\HtmlCache'
    ],
    // 应用结束
    'app_end'      => [
        // 访问记录
        'app\\cms\\behavior\\Visit'
    ],
];
