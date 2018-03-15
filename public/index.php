<?php
/**
 *
 * 应用入口文件
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

namespace think;

version_compare(PHP_VERSION, '5.6.0', '>=') or die('require PHP >= 5.6.0 !');
// CB|Alpha 内测版 RC|Beta 正式候选版 Demo 演示版 Stable 稳定版 Release 正式版
define('NP_VERSION', '2.0.5 CB2115');
// 调试开关
define('APP_DEBUG', true);
// 设置超时时间
set_time_limit(300);
// 设置运行内存
ini_set('memory_limit', '8M');
// 开启gzip压缩
if (extension_loaded('zlib')) ob_start('ob_gzhandler');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
