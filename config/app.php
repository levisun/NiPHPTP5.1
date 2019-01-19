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
    'app_debug'            => APP_DEBUG,                                        // 应用调试模式
    'app_trace'            => APP_DEBUG,                                        // 应用Trace
    'default_timezone'     => 'PRC',                                            // 默认时区

    'default_filter'       => 'safe_filter_strict',                             // 默认全局过滤方法 用逗号分隔多个
    'content_filter'       => 'safe_filter',

    'lang_switch_on'       => true,                                             // 是否开启多语言
    'default_lang'         => 'zh-cn',                                          // 默认语言
    'lang_list'            => ['zh-cn', 'en-us'],

    'default_module'       => 'cms',                                            // 默认模块名
    'deny_module_list'     => ['common'],                                       // 禁止访问模块
    'default_module'       => 'cms',                                            // 默认模块名
    'default_controller'   => 'Index',                                          // 默认控制器名
    'default_action'       => 'index',                                          // 默认操作名
    'pathinfo_depr'        => '/',                                              // pathinfo分隔符
    'url_html_suffix'      => 'html',                                           // URL伪静态后缀
    'route_complete_match' => true,                                             // 路由使用完整匹配
    'url_route_must'       => false,                                            // 是否强制使用路由
    'url_domain_deploy'    => true,                                             // 域名部署
    'url_lazy_route'       => true,                                             // 开启路由延迟解析
    'route_rule_merge'     => true,                                             // 合并分组路由规则

    // 异常页面的模板文件
    'exception_tmpl'       => env('root_path') . 'public/theme/abort/think_exception.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'        => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'       => false,

    'http_exception_template' => [
        403 => env('root_path') . 'public/theme/abort/403.html',           // 禁止访问
        404 => env('root_path') . 'public/theme/abort/404.html',           // 找不到
        500 => env('root_path') . 'public/theme/abort/500.html',           // 服务器错误
        502 => env('root_path') . 'public/theme/abort/502.html',           // 网关错误
    ],
];
