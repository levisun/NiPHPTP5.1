<?php
/**
 *
 * 应用入口文件
 *
 * @package   NiPHPCMS
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/7
 *
 * CB|Alpha 内测版
 * RC|Beta  正式候选版
 * Demo     演示版
 * Stable   稳定版
 * Release  正式版
 */
namespace think;

// PHP版本支持
version_compare(PHP_VERSION, '5.6.0', '>=') or die('PHP VERSION >= 5.6.0!');

define('APP_DEBUG', false);
define('NP_VERSION', '2.0.1 Alpha');
define('DS', DIRECTORY_SEPARATOR);
define('NP_C_PREFIX', substr(md5(__DIR__), 0, 9));
define('NP_CS_PREFIX', strtoupper(substr(NP_C_PREFIX, -3)));
header('X-Powered-By: NiPHP');

if (APP_DEBUG) {
    set_time_limit(30);
    ini_set('memory_limit', '16M');
} else {
    set_time_limit(300);
    ini_set('memory_limit', '64M');
}

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
