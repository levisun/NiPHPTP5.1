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




Route::miss('index/abort');


Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::domain('www', function(){
    Route::miss('index/abort');
    Route::get('/', 'index/index');
    Route::get('/:name$', 'index/index');
    Route::get('/:name/:cid$', 'index/index');
    Route::get('/:name/:cid/:id$', 'index/index');

    // Route::cache(true);
})
->bind('index')
->ext('html');

Route::domain('admin', function(){
    Route::get('/', 'admin/index');
    Route::get('/:logic/:name$', 'admin/index');
})
->bind('admin')
->ext('html');

Route::domain('api', function(){
    Route::rule('/', function(){
        throw new HttpException(404);
    });

    Route::miss('api/abort');
    Route::rule('query/:name$', 'api/query');
    Route::rule('handle/:name$', 'api/handle');
    Route::rule('upload/:name$', 'api/upload');

    $headers = [
        'Access-Control-Allow-Origin'  => Request::server('HTTP_ORIGIN', '*'),
        'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE',
        'Access-Control-Allow-Headers' => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, Authentication, Accept'
    ];
    if (Request::isOptions()) {
        $headers['Access-Control-Max-Age'] = 1728000;
    }

    Route::allowCrossDomain(true, $headers);
})
->bind('api')
->ext('html');
