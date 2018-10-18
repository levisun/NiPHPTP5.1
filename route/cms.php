<?php
/**
 *
 * admin模块 路由配置
 *
 * @package   NiPHPCMS
 * @category  route
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */

Route::domain(['www', 'm'], function(){
    Route::rule('index', 'index/index');

    Route::rule('list/:cid$',   'index/entry');
    Route::rule('link/:cid$',   'index/entry');
    Route::rule('go/:cid/:id$', 'index/go');

    Route::rule('channel/:cid$',  'index/index/channel');
    Route::rule('feedback/:cid$', 'index/feedback');
    Route::rule('message/:cid$',  'index/message');
    Route::rule('search/:cid$',   'index/search');

    Route::rule('article/:cid/:id$',  'index/article');
    Route::rule('picture/:cid/:id$',  'index/article');
    Route::rule('download/:cid/:id$', 'index/article');
    Route::rule('product/:cid/:id$',  'index/article');
    Route::rule('page/:cid$',         'index/article');

    Route::rule('tags$', 'index/tags');

    Route::rule('error/:code$', 'index/abort');

    Route::group('api', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
    })->prefix('api/');
})
->bind('cms')
->cache(APP_DEBUG ? false : 28800)
->ext('html');
