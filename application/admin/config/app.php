<?php
/**
 *
 * 模块设置
 *
 * @package   NiPHP
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
    'default_theme'       => '201901',
    // 默认语言
    'default_lang'        => 'zh-cn',
    // 是否开启多语言
    'lang_switch_on'      => true,

    // 默认控制器名
    'default_controller'  => 'account',
    // 默认操作名
    'default_action'      => 'login',

    'user_auth_founder'   => 1,
    'user_auth_key'       => 'auth_id',                                         // 认证key
    'user_auth_on'        => true,                                              // 是否需要认证
    'user_auth_type'      => 2,                                                 // 验证类型
    'not_auth_controller' => ['account'],                                       // 无需认证的控制器
    // 无需认证的方法
    'not_auth_method'     => [
        'login',
        'logout',
        'verify'
    ],
    // 无需认证的操作
    'not_auth_action'     => [
        'type',
        'category',
        'getTablesName',
        'parent',
        'models',
        'level',
        'manage',
        'sort',
        'role',
        'region',
        'node',
    ],
];
