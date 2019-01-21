<?php
/**
 *
 * API 路由配置
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/12
 */

Route::domain('admin', function(){
    Route::get('/', 'account/login');
    Route::get('index', 'account/login');
})
->bind('admin')
->ext('html')
->middleware([
    'app\\common\\middleware\\Concurrent::class'
])
->cache(false);
