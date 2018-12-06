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
    Route::rule('/', 'index/index');
    Route::rule('index', 'index/index');
    Route::rule('list/:cid$', 'index/entry');
    Route::rule('link/:cid$', 'index/entry');
    Route::rule('search$', 'index/search');
    Route::rule('channel/:cid$', 'index/channel');
    Route::rule('feedback/:cid$', 'index/feedback');
    Route::rule('message/:cid$', 'index/message');
    Route::rule('comment/:cid/:id$', 'index/comment');
    Route::rule('article/:cid/:id$', 'index/article');
    Route::rule('picture/:cid/:id$', 'index/article');
    Route::rule('download/:cid/:id$', 'index/article');
    Route::rule('product/:cid/:id$', 'index/article');
    Route::rule('page/:cid$', 'index/article');

    Route::rule('go/:cid/[:id]$',  'index/go');
    Route::rule('tags/:id$', 'index/tags');

    Route::rule('getipinfo', 'index/getipinfo');

    Route::rule('error/:code$', 'index/abort');

    Route::rule('caiji', 'index/caiji');
})
->bind('cms')
->cache(APP_DEBUG ? false : 1200);
