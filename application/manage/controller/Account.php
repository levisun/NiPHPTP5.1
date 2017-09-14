<?php
/**
 *
 * 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Account.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\controller;

use app\manage\controller\Base;

use app\manage\controller\account\Login as AccountLogin;

class Account extends Base
{

    /**
     * 登录
     * @access public
     * @param
     * @return void
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $result =
            action(
                'Login/login',
                $this->request->only(['username', 'password'], 'post'),
                'controller\account'
            );
        }

        return $this->fetch();
    }

    public function logout()
    {
        # code...
    }
}
