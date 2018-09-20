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
    'p'       => '\d+',
]);

// 后台
Route::domain(['admin'])
->bind('admin')
->ext('do');

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

Route::domain(['www', 'm', 'wechat'], [
    'getipinfo'          => 'index/getipinfo',

    // 列表页[文章 图片 下载 反馈 留言 产品 友链]
    'list/:cid$'         => 'index/entry',
    'link/:cid$'         => 'index/entry',
    'go/:cid/:id$'       => 'index/go',

    // 频道页
    'channel/:cid$'      => 'index/channel',
    // 反馈页
    'feedback/:cid$'     => 'index/feedback',
    // 留言页
    'message/:cid$'      => 'index/message',
    // 搜索页
    'search/[:q]$'       => 'index/search',

    // 文章详情页
    'article/:cid/:id$'  => 'index/article',
    // 图片详情页
    'picture/:cid/:id$'  => 'index/article',
    // 下载详情页
    'download/:cid/:id$' => 'index/article',
    // 产品详情页
    'product/:cid/:id$'  => 'index/article',
    // 单页详情页
    'page/:cid'          => 'index/article',

    'tags'               => 'index/tags',

])
->bind('cms')
->ext('html')
->cache(!APP_DEBUG);

return [
];
