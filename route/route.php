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
$expire = config('cache.expire');

// CMS 模块
Route::domain(['www', 'm'], function(){
    Route::miss('index/abort');
    Route::rule('/', 'index/index');
    Route::rule('index', 'index/index');
    Route::rule('search$', 'index/search');

    Route::rule('list-:cid$', 'index/entry');
    Route::rule('link-:cid$', 'index/entry');
    Route::rule('channel-:cid$', 'index/channel');
    Route::rule('feedback-:cid$', 'index/feedback');
    Route::rule('message-:cid$', 'index/message');
    Route::rule('tags-:id$', 'index/tags');

    Route::rule('comment-:cid-:id$', 'index/comment');
    Route::rule('article-:cid-:id$', 'index/article');
    Route::rule('download-:cid-:id$', 'index/article');
    Route::rule('page-:cid$', 'index/article');
    Route::rule('picture-:cid-:id$', 'index/article');
    Route::rule('product-:cid-:id$', 'index/article');

    Route::rule('go-:cid-[:id]$',  'index/go');

    Route::rule('getipinfo', 'index/getipinfo');

    Route::rule('error-:code$', 'index/abort');

    Route::rule('caiji', 'index/caiji');

    // AJAX路由
    Route::rule('ajax-query',  'ajax/query');
    Route::rule('ajax-settle', 'ajax/settle');
    Route::rule('ajax-upload', 'ajax/upload');
    Route::rule('ajax-getipinfo', 'ajax/getipinfo');
})
->bind('cms')
->ext('html')
->cache(APP_DEBUG ? false : $expire);

// BOOK 模块
Route::domain('book', function(){
    Route::rule('/', 'index/index');
    Route::rule('index', 'index/index');

    Route::rule('list-:bid$',        'index/entry');
    Route::rule('article-:bid-:id$', 'index/article');

    Route::rule('search-:q$',        'index/search');
})
->bind('book')
->cache(APP_DEBUG ? false : $expire);

// ADMIN 模块
Route::domain('admin', function(){
    Route::rule('/', 'account/login');
    Route::rule('index', 'account/login');

    // AJAX路由
    Route::group('ajax', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
    })->prefix('ajax/');
})
->bind('admin')
->ext('do')
->cache(false);

// API 模块
Route::domain('api', function(){
    Route::miss('api/abort');
    Route::rule('/', 'api/abort')->allowCrossDomain();

    Route::rule('getipinfo', 'api/getipinfo', 'GET|POST')->allowCrossDomain();
    Route::rule('settle', 'api/settle', 'POST')->allowCrossDomain();
    Route::rule('upload', 'api/upload', 'POST')->allowCrossDomain();

    Route::rule('cms/query',  'cms/query', 'GET|POST')->allowCrossDomain();
    Route::rule('book/query',  'book/query', 'GET|POST')->allowCrossDomain();
})
->bind('api')
->ext('do')
->cache(APP_DEBUG ? false : $expire);

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
