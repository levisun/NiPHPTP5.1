<?php
/**
 *
 * 注销 - 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Logout.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\controller\account;

class Logout
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 注销
     * @access public
     * @param
     * @return void
     */
    public function logout()
    {
        $logout = logic('Logout', 'logic\account');
        $logout->removeAuth();
    }
}
