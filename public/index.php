<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

version_compare(PHP_VERSION, '5.6.0', '>=') or die('require PHP >= 5.6.0 !');
// CB|Alpha 内测版 RC|Beta 正式候选版 Demo 演示版 Stable 稳定版 Release 正式版
define('NP_VERSION', '1.0.1 CB18');
define('APP_DEBUG', true);
set_time_limit(300);
ini_set('memory_limit', '8M');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
