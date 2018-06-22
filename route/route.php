<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::pattern([
    'cid' => '\d+',
    'id'  => '\d+',
]);

// Route::name('admin')->cache('__URL__', 600);
// Route::get('admin/settings/basic', 'admin/settings/basic')->cache(3600);
// Route::rule('admin/settings/basic', 'admin/settings/basic')->cache(3600);
// Route::rule('admin/settings/info', 'admin/settings/info')->cache(3600);

return [
    // 全局变量规则定义
    '__pattern__' => [
        'method' => '\w+',
        'cid'    => '\d+',
        'id'     => '\d+',
    ],

    '__domain__' => [
        'admin' => 'admin',
        'my'    => 'member',
    ],

    // 'admin/settings/basic' => [
    //     'admin/settings/basic',
    //     ['cache' => 30],
    // ],

    '/' => 'index',
];
