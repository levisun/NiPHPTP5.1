<?php
/**
 *
 * 界面 - 控制器
 *
 * @package   NiPHP
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Theme extends Base
{

    /**
     * 网站
     * @access public
     * @param
     * @return mixed
     */
    public function cms()
    {
        return $this->fetch();
    }

    /**
     * 会员
     * @access public
     * @param
     * @return mixed
     */
    public function member()
    {
        return $this->fetch();
    }

    /**
     * 商城
     * @access public
     * @param
     * @return mixed
     */
    public function mall()
    {
        return $this->fetch();
    }
}
