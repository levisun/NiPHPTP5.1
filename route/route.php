<?php
/**
 *
 * 全局 路由配置
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/12
 */

// 全局变量规则
Route::pattern([
    'operate' => '\w+',
    'code'    => '\d+',
    'cid'     => '\d+',
    'bid'     => '\d+',
    'pid'     => '\d+',
    'id'      => '\d+',
    'p'       => '\d+',
]);

// 进制cdn|css|img|js等二级域名直接访问网站
Route::domain([
    'cdn', 'css', 'img', 'js'
], function(){
    abort(404);
});

// API接口路由
Route::group('api', function(){
    Route::rule('query',  'query');
    Route::rule('settle', 'settle');
    Route::rule('upload', 'upload');
})->prefix('api/');

// Route::miss('index/abort');

// Route::domain('api', function(){
//     Route::group('admin', function(){
//         Route::rule('query',  'query');
//         Route::rule('settle', 'settle');
//         Route::rule('upload', 'upload');
//     })->prefix('admin/api/');


//     Route::group('cms', function(){
//         Route::rule('query',  'query');
//         Route::rule('settle', 'settle');
//         Route::rule('upload', 'upload');
//         Route::rule('getipinfo', 'getipinfo');
//     })->prefix('cms/api/');
// });

// Route::domain('my', [
// ])
// ->bind('member')
// ->ext('do');

// Route::domain('mall', [
// ])
// ->bind('mall')
// ->ext('html');


