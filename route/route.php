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

// 禁止cdn|css|img|js等二级域名直接访问网站
Route::domain([
    'cdn', 'css', 'img', 'js'
], function(){
    abort(404);
});

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

// API 模块
Route::domain('api', function(){
    Route::miss('api/abort');

    $refer = parse_url(request()->server('HTTP_ORIGIN'));
    if (empty($refer['host']) || !in_array($refer['host'], config('whitelist'))) {
        abort(404);
    }

    Route::group('', function(){
        $expire = config('cache.expire');

        Route::rule('getipinfo', 'api/getipinfo')->cache(APP_DEBUG ? false : $expire);
        Route::rule('query/cms', 'api/index')->cache(APP_DEBUG ? false : $expire);




        Route::rule('query/admin', 'api/index');
        Route::rule('handle/admin', 'api/index');
    })
    ->allowCrossDomain(true, [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Origin'      => $refer['scheme'] . '://' . $refer['host'],
    ]);
})
->bind('api')
->ext('html');

// CMS 模块
Route::domain(['www', 'm'], function(){
    Route::miss('index/abort');
    Route::get('/', 'index/index');
    Route::get('index', 'index/index');
    Route::get('search$', 'index/search');

    Route::get('list/<cid>$', 'index/entry');
    Route::get('link/<cid>$', 'index/entry');
    Route::get('channel/<cid>$', 'index/channel');
    Route::get('feedback/<cid>$', 'index/feedback');
    Route::get('message/<cid>$', 'index/message');
    Route::get('tags/<id>$', 'index/tags');

    Route::get('comment/<cid>/<id>$', 'index/comment');
    Route::get('article/<cid>/<id>$', 'index/article');
    Route::get('download/<cid>/<id>$', 'index/article');
    Route::get('page/<cid>$', 'index/article');
    Route::get('picture/<cid>/<id>$', 'index/article');
    Route::get('product/<cid>/<id>$', 'index/article');

    Route::get('go/<cid>/<id>$',  'index/go');

    Route::get('error/<code>$', 'index/abort');

    Route::get('caiji', 'index/caiji');
})
->bind('cms')
->ext('html')
->cache(false);

// BOOK 模块
Route::domain('book', function(){
    Route::get('/', 'index/index');
    Route::get('index', 'index/index');

    Route::get('list/<bid>$',        'index/entry');
    Route::get('article/<bid>/<id>$', 'index/article');

    Route::get('search/<q>$',        'index/search');
})
->bind('book')
->ext('html')
->cache(false);

// ADMIN 模块
Route::domain('admin', function(){
    Route::get('/', 'account/login');
    Route::get('index', 'account/login');

    // AJAX路由
    Route::group('ajax', function(){
        Route::post('query',  'query');
        Route::post('settle', 'settle');
        Route::post('upload', 'upload');
    })->prefix('ajax/');
})
->bind('admin')
->ext('html')
->cache(false);
