<?php
if (version_compare(PHP_VERSION, '5.6.0', '<')) die('require PHP >= 5.6.0 !');
define('APP_DEBUG', true);
// CB|Alpha 内测版 RC|Beta 正式候选版 Demo 演示版 Stable 稳定版 Release 正式版
define('NP_VERSION', '1.0.18 Alpha');
set_time_limit(300);
if (APP_DEBUG) @ini_set('memory_limit', '8M'); else @ini_set('memory_limit', '16M');
define('APP_PATH', __DIR__ . '/../application/');
require __DIR__ . '/../thinkphp/start.php';
