<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// Route::miss('index/abort');

Route::pattern([
    'operate' => '\w+',
    'code'    => '\d+',
    'cid'     => '\d+',
    'pid'     => '\d+',
    'id'      => '\d+',
    'p'       => '\d+',
]);

Route::domain('my', [
])
->bind('member')
->ext('do');

Route::domain('mall', [
])
->bind('mall')
->ext('html');

Route::domain('api.wechat', [
])
->bind('wechat')
->ext('do');
