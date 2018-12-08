<?php
/**
 *
 * 用户 - 控制器
 *
 * @package   NiPHP
 * @category  application\admin\controller
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
     * 会员管理
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function member($operate = '')
    {
        $this->assign('button_search', 1);
        $tpl = $operate ? 'member_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 会员等级管理
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function level($operate = '')
    {
        $tpl = $operate ? 'level_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 管理员
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function admin($operate = '')
    {
        $this->assign('button_search', 1);
        $tpl = $operate ? 'admin_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 管理员组
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function role($operate = '')
    {
        $tpl = $operate ? 'role_' . $operate : '';
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
