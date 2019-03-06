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
use app\server\Rbac;

class Auth
{
    protected $logic;
    protected $class;
    protected $action;

    protected function authenticate($_logic, $_class, $_action)
    {

    }

    /**
     * 构造方法
     * @access public
     * @param
     * @return void
     */
    public function __construct()
    {
        $this->className = basename(str_replace('\\', '/', get_class($this)));

        halt($this->className);
        // 用户权限校验
        if (session('?admin_auth_key')) {
            // (new Rbac)->authenticate('admin', __CLASS__)
        }
    }
}
