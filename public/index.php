<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

version_compare(PHP_VERSION, '7.1.0', '>=') or die('PHP VERSION >= 7.1.0!');
if (!extension_loaded('PDO')) die('PDO');
define('APP_DEBUG', true);
define('NP_VERSION', '2.0.1.20181222 Alpha');
define('AUTHKEY', '1286755f348733a76a252efb3848fbab9f3e9f81');



require __DIR__ . '/../vendor/autoload.php';

// 执行应用并响应
(new App())->autoMulti()->run()->send();
