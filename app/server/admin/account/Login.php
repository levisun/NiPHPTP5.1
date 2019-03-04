<?php
/**
 *
 * API接口层
 * 权限判断
 *
 * @package   NiPHP
 * @category  app\server\admin\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server\admin\account;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\server\admin\account\Auth;

class Login extends Auth
{
    /**
     * 构造方法
     * @access public
     * @param
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        # code...
    }
}
