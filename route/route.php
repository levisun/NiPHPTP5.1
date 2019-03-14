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
use think\exception\HttpException;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;


Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});



Route::miss('abort/error');

// 错误页面
// code错误代码
// 403 404
// 权限错误页
Route::rule('error', 'abort/error');
Route::rule('error/:code$', 'abort/error');
Route::rule('authority', 'abort/authority');



Route::domain('www', function(){
    Route::get('/', 'cms/index');
    Route::get('list/:name/:cid$', 'cms/catalog');
    Route::get('details/:name/:cid/:id$', 'cms/details');
})
->bind('cms')
->ext('html');



Route::domain('admin', function(){
    Route::get('/', 'admin/index');
    Route::get('logic', 'admin/logic');
})
->bind('admin')
->ext('html');



Route::domain('api', function(){
    Route::rule('/', function(){
        return '404';
    });

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

Route::domain('cdn', function(){

});
