<?php
/**
 *
 * 模块设置
 *
 * @package   NiPHPCMS
 * @category  application\admin\config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
return [


    // icon
    'icon' => [
        'settings' => 'cogs',
        'theme'    => 'dashboard',
        'category' => 'reorder',
        'content'  => 'edit',
        'user'     => 'group',
        'wechat'   => 'comments',
        'mall'     => 'shopping-cart',
        'book'     => 'book',
        'expand'   => 'wrench',
    ],

    // 模板
    'default_theme'           => 'simplify',
    // 默认语言
    'default_lang'            => 'zh-cn',
    // 是否开启多语言
    'lang_switch_on'          => true,

    // 默认控制器名
    'default_controller'      => 'account',
    // 默认操作名
    'default_action'          => 'login',
    // URL伪静态后缀
    'url_html_suffix'         => 'do',

    // 认证key
    'user_auth_key'           => 'auth_id',
    // 是否需要认证
    'user_auth_on'            => true,
    // 验证类型
    'user_auth_type'          => 2,
    // 需要认证模块
    'require_auth_module'     => '',
    // 无需认证模块
    'not_auth_module'         => 'admin',
    // 需要认证的控制器
    'require_auth_controller' => '',
    // 无需认证的控制器
    'not_auth_controller'     => 'account',
    // 需要认证的方法
    'require_auth_action'     => '',
    // 无需认证的方法
    'not_auth_action'         => 'login,logout,verify',
];
