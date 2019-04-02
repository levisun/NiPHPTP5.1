<?php
/**
 *
 * API接口层
 * 权限判断
 *
 * @package   NICMS
 * @category  app\logic\admin\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\admin\account;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\logic\admin\Base;

class Login extends Base
{

    public function login(): arrray
    {
        if ($result = $this->__authenticate('account', 'login', 'login')) {
            return $result
        }


    }
}
