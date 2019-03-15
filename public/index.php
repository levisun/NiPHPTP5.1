<?php
/**
 *
 * 应用入口文件
 *
 * @package   NiPHPCMS
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 *
 * CB|Alpha 内测版
 * RC|Beta  正式候选版
 * Demo     演示版
 * Stable   稳定版
 * Release  正式版
 */

namespace think;

version_compare(PHP_VERSION, '7.1.0', '>=') or die('系统需要PHP7.1以上版本! 当前PHP版本:' . PHP_VERSION . '.');
extension_loaded('pdo') or die('请开启 pdo 模块!');
extension_loaded('pdo_mysql') or die('请开启 pdo_mysql 模块!');
set_time_limit(30);
ini_set('memory_limit', '32M');
header('X-Powered-By: CB');

define('APP_DEBUG', true);
define('NP_VERSION', '1.5.2 CB 212');
define('TP_VERSION', '5.2.0RC1');

require __DIR__ . '/../vendor/autoload.php';

version_compare(App::VERSION, TP_VERSION, '>=') or die('系统需要 ThinkPHP' . TP_VERSION . '以上版本! 当前ThinkPHP版本:' . App::VERSION . '.');

// 执行应用并响应
(new App())->debug(APP_DEBUG)->run()->send();
