<?php
/**
 *
 * 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Account extends Base
{

    /**
     * 登录
     * @access public
     * @param
     * @return mixed
     */
    public function login()
    {
        return $this->fetch();
    }
}
