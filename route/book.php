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

Route::domain('book', function(){
    Route::rule('/', 'index/index');
    Route::rule('index', 'index/index');

    Route::rule('list/:bid$',        'index/entry');
    Route::rule('article/:bid/:id$', 'index/article');

    Route::rule('search/:q$',        'index/search');


    Route::group('api', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        // Route::rule('upload', 'upload');
    })->prefix('api/');
})
->bind('book')
->cache(APP_DEBUG ? false : 1200);
