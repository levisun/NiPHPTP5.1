<?php
/**
 *
 * 模板设置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // 模板后缀
    'view_suffix'   => 'html',
    // 模板文件名分隔符
    'view_depr'     => DIRECTORY_SEPARATOR,
    // 布局
    'layout_on'     => true,
    // 布局入口文件名
    'layout_name'   => 'layout',
    // 布局输出替换变量
    'layout_item'   => '{__CONTENT__}',
    // 去除模板文件里面的html空格与换行
    'strip_space'   => true,
    // 模板编译缓存
    'tpl_cache'     => !APP_DEBUG,
    // 模板渲染缓存
    'display_cache' => !APP_DEBUG,
    // php标签
    'tpl_deny_php'  => true,
    // 模板引擎禁用函数
    'tpl_deny_func_list' => 'echo,exit,die,var_export,var_dump',
];
