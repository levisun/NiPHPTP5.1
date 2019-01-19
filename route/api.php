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

Route::domain('api', function(){
    Route::miss('api/abort');

    $expire = config('cache.expire');

    Route::rule('getipinfo', 'api/getipinfo')->cache(APP_DEBUG ? false : $expire);
    Route::rule('visit', 'api/visit');

    Route::rule('admin/query', 'api/index');
    Route::rule('admin/handle', 'api/index');


    Route::rule('cms/query', 'api/index')->cache(APP_DEBUG ? false : $expire);
})
->bind('api')
->middleware([
    'app\\common\\middleware\\Concurrent::class',
    'app\\common\\middleware\\RemoveRunGarbage::class'
])
->ext('html')
->cache(false)
->allowCrossDomain();
