<?php
/**
 *
 * 设置 - 控制器
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

class Settings extends Base
{

    /**
     * 系统信息
     * @access public
     * @param
     * @return mixed
     */
    public function info()
    {
        return $this->fetch();
    }

    /**
     * 基础设置
     * @access public
     * @param
     * @return mixed
     */
    public function basic()
    {
        return $this->fetch();
    }

    /**
     * 语言设置
     * @access public
     * @param
     * @return mixed
     */
    public function lang()
    {
        return $this->fetch();
    }

    /**
     * 图片设置
     * @access public
     * @param
     * @return mixed
     */
    public function image()
    {
        return $this->fetch();
    }

    /**
     * 安全与效率设置
     * @access public
     * @param
     * @return mixed
     */
    public function safe()
    {
        return $this->fetch();
    }

    /**
     * 邮箱设置
     * @access public
     * @param
     * @return mixed
     */
    public function email()
    {
        return $this->fetch();
    }
}
