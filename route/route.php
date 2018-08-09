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

Route::pattern([
    'operate' => '\w+',
    'cid'     => '\d+',
    'pid'     => '\d+',
    'id'      => '\d+',
]);

Route::domain('admin', [
])->bind('admin')->ext('do');

Route::domain('www', [
    'channel/:cid$'  => 'index/channel',
    'feedback/:cid$' => 'index/feedback',
    'message/:cid$'  => 'index/message',
    ':operate/:cid$' => 'index/entry'
])->bind('cms')->ext('html')->cache(3600);


Route::domain('my', [
])->bind('cms')->ext('shtml');

return [
];
