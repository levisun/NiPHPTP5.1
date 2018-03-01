<?php
/**
 *
 * 注销 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\account;

class Logout
{

    /**
     * 注销
     * @access public
     * @param
     * @return void
     */
    public function logout()
    {
        create_action_log('', 'admin_logout');
        session(null);
        cookie(null);
        return true;
    }
}
