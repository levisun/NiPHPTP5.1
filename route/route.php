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

// Route::miss('index/abort');

Route::pattern([
    'operate' => '\w+',
    'code'    => '\d+',
    'cid'     => '\d+',
    'bid'     => '\d+',
    'pid'     => '\d+',
    'id'      => '\d+',
    'p'       => '\d+',
]);

Route::domain('api', function(){
    Route::group('admin', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
    })->prefix('admin/api/');


    Route::group('cms', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
        Route::rule('getipinfo', 'getipinfo');
    })->prefix('cms/api/');
});

Route::domain('my', [
])
->bind('member')
->ext('do');

Route::domain('mall', [
])
->bind('mall')
->ext('html');

Route::domain('api.wechat', [
])
->bind('wechat')
->ext('do');
