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

Route::domain('admin', function(){
    // Route::rule('/', 'account/login');
    Route::rule('index', 'account/login');

    Route::group('api', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
    })->prefix('api/');

    Route::group('settings', function(){
        Route::rule('info',  'info');
        Route::rule('basic', 'basic');
        Route::rule('lang',  'lang');
        Route::rule('image', 'image');
        Route::rule('safe',  'safe');
        Route::rule('email', 'email');
    })->prefix('settings/');

    Route::group('theme', function(){
        Route::rule('cms',    'cms');
        Route::rule('member', 'member');
        Route::rule('mall',   'mall');
    })->prefix('theme/');

    Route::group('category', function(){
        Route::rule('category', 'category');
        Route::rule('model',    'model');
        Route::rule('fields',   'fields');
        Route::rule('type',     'type');
    })->prefix('category/');

    Route::group('content', function(){
        Route::rule('content',  'content');
        Route::rule('banner',   'banner');
        Route::rule('ads',       'ads');
        Route::rule('comment',  'comment');
        Route::rule('cache',    'cache');
        Route::rule('recycle',  'recycle');
    })->prefix('content/');

    Route::group('user', function(){
        Route::rule('member', 'member');
        Route::rule('level',  'level');
        Route::rule('admin',  'admin');
        Route::rule('role',   'role');
        Route::rule('node',   'node');
    })->prefix('user/');

    Route::group('wechat', function(){
        Route::rule('keyword',   'keyword');
        Route::rule('auto',      'auto');
        Route::rule('attention', 'attention');
        Route::rule('config',    'config');
        Route::rule('menu',      'menu');
    })->prefix('wechat/');

    Route::group('mall', function(){
        Route::rule('goods',    'goods');
        Route::rule('orders',   'orders');
        Route::rule('category', 'category');
        Route::rule('type',     'type');
        Route::rule('brand',    'brand');
        Route::rule('comment',  'comment');
        Route::rule('account',  'account');
        Route::rule('grecycle', 'grecycle');
        Route::rule('settings', 'settings');
    })->prefix('mall/');

    Route::group('book', function(){
        Route::rule('book', 'book');
        Route::rule('type', 'type');
        Route::rule('user', 'user');
    })->prefix('book/');

    Route::group('expand', function(){
        Route::rule('log',      'log');
        Route::rule('databack', 'databack');
        Route::rule('upgrade',  'upgrade');
        Route::rule('elog',     'elog');
        Route::rule('visit',    'visit');
    })->prefix('expand/');

    Route::group('api', function(){
        Route::rule('query',  'query');
        Route::rule('settle', 'settle');
        Route::rule('upload', 'upload');
    })->prefix('api/');
})
->bind('admin')
->cache(false);

