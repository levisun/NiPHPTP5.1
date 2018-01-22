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
 * @since     2017/12
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Account extends Base
{

    public function login()
    {
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
        session(null);
        cookie(null);
        $this->redirect(url('login'));
    }
}
