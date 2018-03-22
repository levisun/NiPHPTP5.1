<?php
/**
 *
 * 用户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2028/03
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class User extends Base
{

    /**
     * 管理员
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function admin($operate = '')
    {
        $tpl = $operate ? 'admin_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 系统节点
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function node($operate = '')
    {
        $tpl = $operate ? 'node_' . $operate : '';
        return $this->fetch($tpl);
    }
}
