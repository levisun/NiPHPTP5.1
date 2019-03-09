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

define('APP_DEBUG', true);
define('NP_VERSION', '1.5.2 CB 212');

version_compare(PHP_VERSION, '7.1.0', '>=') or die('PHP VERSION >= 7.1.0!');
if (!extension_loaded('PDO')) die('PDO');
set_time_limit(30);
ini_set('memory_limit', '32M');
header('X-Powered-By: CB');

require __DIR__ . '/../vendor/autoload.php';

// 执行应用并响应
(new App())->debug(APP_DEBUG)->run()->send();
