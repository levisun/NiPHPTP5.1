<?php
return [
    // 模板
    'default_theme'           => 'simplify',

    // icon
    'icon'                    => [
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

	// 默认控制器名
    'default_controller'      => 'Account',
    // 默认操作名
    'default_action'          => 'login',

    // 认证key
    'user_auth_key'           => 'auth_id',
    // 是否需要认证
    'user_auth_on'            => true,
    // 验证类型
    'user_auth_type'          => 1,
    // 需要认证模块
    'require_auth_module'     => '',
    // 无需认证模块
    'not_auth_module'         => 'admin',
    // 需要认证的控制器
    'require_auth_controller' => '',
    // 无需认证的控制器
    'not_auth_controller'     => 'Account',
    // 需要认证的方法
    'require_auth_action'     => '',
    // 无需认证的方法
    'not_auth_action'         => 'login,logout,verify',
];
