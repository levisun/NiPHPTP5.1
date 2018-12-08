<?php
/**
 *
 * api模块 路由配置
 *
 * @package   NiPHP
 * @category  route
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/12
 */

Route::domain('api', function(){
    // ->header('Set-Cookie', 'id=a3fWa')
    Route::miss('api/abort');
    Route::rule('/', 'api/abort')->allowCrossDomain();
    Route::rule('getipinfo', 'api/getipinfo')->allowCrossDomain();

    Route::rule('query',  'cms/query')->allowCrossDomain();

    Route::rule('settle', 'api/settle')->allowCrossDomain();
    Route::rule('upload', 'api/upload')->allowCrossDomain();
})
->bind('api')
->ext('do')
->cache(APP_DEBUG ? false : 1200);
