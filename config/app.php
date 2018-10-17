<?php
/**
 *
 * 应用配置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

return [
    // 应用调试模式
    'app_debug'            => APP_DEBUG,
    // 应用Trace
    'app_trace'            => APP_DEBUG,
    // 默认时区
    'default_timezone'     => 'PRC',
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'       => 'trim,strip_tags,safe_filter',
    'content_filter'       => 'trim,safe_filter,htmlspecialchars',
    // 是否开启多语言
    'lang_switch_on'       => true,
    // 默认语言
    'default_lang'         => 'zh-cn',
    'lang_list'            => ['zh-cn', 'en-us'],
    // 默认模块名
    'default_module'       => 'cms',
    // 禁止访问模块
    'deny_module_list'     => ['common', 'admin'],
    // 默认控制器名
    'default_controller'   => 'Index',
    // 默认操作名
    'default_action'       => 'index',
    // pathinfo分隔符
    'pathinfo_depr'        => '/',
    // URL伪静态后缀
    'url_html_suffix'      => 'do|htm|html',
    // 路由使用完整匹配
    'route_complete_match' => true,
    // 是否强制使用路由
    'url_route_must'       => true,
    // 域名部署
    'url_domain_deploy'    => true,

    // 异常页面的模板文件
    'exception_tmpl'       => Env::get('root_path') . 'public/theme/abort/think_exception.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'        => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'       => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'     => '',

    'http_exception_template' => [
        403 => Env::get('root_path') . 'public/theme/abort/403.html',           // 禁止访问
        404 => Env::get('root_path') . 'public/theme/abort/404.html',           // 找不到
        500 => Env::get('root_path') . 'public/theme/abort/500.html',           // 服务器错误
        502 => Env::get('root_path') . 'public/theme/abort/502.html',           // 网关错误
    ],
];
