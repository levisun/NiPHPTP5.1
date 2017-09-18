<?php
if (version_compare(PHP_VERSION, '5.6.0', '<')) die('require PHP >= 5.6.0 !');
define('APP_DEBUG', true);
define('NP_VERSION', '1.0.7 Alpha'); /*CB 内测版 beta公测版 RC正式候选版*/
define('APP_PATH', __DIR__ . '/../application/');
require __DIR__ . '/../thinkphp/start.php';
