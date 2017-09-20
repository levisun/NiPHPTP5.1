<?php
/**
 *
 * 系统信息 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Info
{

    public function info()
    {
        return [
            'os' => PHP_OS,
            'env' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => PHP_VERSION,
            'db_type' => config('database.type'),
        ];
    }
}
