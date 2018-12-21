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

define('APP_DEBUG', false);



// PHP版本支持
version_compare(PHP_VERSION, '5.6.0', '>=') or die('PHP VERSION >= 5.6.0!');
if (!extension_loaded('PDO')) die('PDO');
if (!is_file(__DIR__ . '/../runtime/install.lock')) {
    header("location:/install.php");
    exit;
}

define('NP_VERSION', '2.0.1_20181207 Alpha');
define('TP_VERSION', '5.1.30 LTS');
define('DS', DIRECTORY_SEPARATOR);
define('NP_CACHE_PREFIX', substr(md5(__DIR__), 0, 7));
define('NP_COOKIE_PREFIX', strtoupper(substr(NP_CACHE_PREFIX, -3)));
header('X-Powered-By: NiPHP ' . NP_VERSION);

if (APP_DEBUG) {
    set_time_limit(30);
    ini_set('memory_limit', '16M');
} else {
    set_time_limit(120);
    ini_set('memory_limit', '64M');
}

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

version_compare(Container::get('app')->version(), TP_VERSION, '>=') or die('THINKPHP VERSION >= ' . TP_VERSION . '!');

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
