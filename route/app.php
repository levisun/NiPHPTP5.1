<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Config;
use think\facade\Request;
use think\facade\Route;

Route::pattern([
    'cid' => '\d+',
    'id' => '\d+',
    'code' => '\d+',
]);

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

// Request::server('HTTP_IF_MODIFIED_SINCE', gmdate('D, d M Y H:i:s') . ' GMT');
// print_r($_SERVER);die();



// 错误页面
// Route::miss('abort/error');
Route::rule('404', 'abort/_404');
// Route::rule('authority', 'abort/authority');

Route::domain('www', function(){
    Route::get('/', 'cms/index');
    Route::get('index', 'cms/index');
    Route::get('list/:name/:cid$', 'cms/lists');
    Route::get('details/:name/:cid/:id$', 'cms/details');
})
->bind('cms')
->cache(Config::get('cache.expire'))
->ext('html');



Route::domain('admin', function(){
    Route::get('/', 'admin/index');
    Route::get(':logic/:controller$', 'admin/index');
    Route::get(':logic/:controller/:action/:id$', 'admin/index');
})
->bind('admin')
->ext('html');



Route::domain('api', function(){
    Route::get(':name$', 'api/query');
    Route::post('handle/:name$', 'api/handle');
    Route::post('upload/:name$', 'api/upload');

    $headers = [
        'Access-Control-Allow-Origin'  => Request::server('HTTP_ORIGIN', '*'),
        // 'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Accept, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With'
    ];
    if (Request::isOptions()) {
        $headers['Access-Control-Max-Age'] = 1728000;
    }

    Route::allowCrossDomain(true, $headers);
})
->bind('api')
->ext('do');
