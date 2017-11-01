<?php
/**
 *
 * 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Account.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
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
        if ($this->request->isPost()) {
            $result = action('Login/login', [], 'account');
            if (true === $result) {
                $this->success(lang('success login'), 'settings/info');
            } else {
                $msg = $result === false ? lang('error') : $result;
                $this->error($msg);
            }
        }

        return $this->fetch();
    }

    /**
     * 注销
     * @access public
     * @param
     * @return void
     */
    public function logout()
    {
        action('Logout/logout', [], 'account');
        $this->redirect(url('login'));
    }
}
