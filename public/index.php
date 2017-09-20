<?php
if (version_compare(PHP_VERSION, '5.6.0', '<')) die('require PHP >= 5.6.0 !');
define('APP_DEBUG', true);
define('NP_VERSION', '1.0.10 Alpha'); /*CB 内测版 beta公测版 RC正式候选版 Alpha内测版 Demo演示版 Beta公测版 Stable稳定版 Release正式版*/
define('APP_PATH', __DIR__ . '/../application/');
require __DIR__ . '/../thinkphp/start.php';
