<?php
/**
 *
 * 微信 - 控制器
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

class Wechat extends Base
{

    /**
     * 关键词回复
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function keyword($operate = '')
    {
        $this->assign('button_search', 1);
        $this->assign('type', 0);

        $tpl = $operate ? 'keyword_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 自动回复
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function auto($operate = '')
    {
        $this->assign('button_search', 1);
        $this->assign('type', 1);

        $tpl = $operate ? 'keyword_' . $operate : 'keyword';
        return $this->fetch($tpl);
    }

    /**
     * 关注回复
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function attention($operate = '')
    {
        $this->assign('button_search', 1);
        $this->assign('type', 1);

        $tpl = $operate ? 'keyword_' . $operate : 'keyword';
        return $this->fetch($tpl);
    }

    /**
     * 菜单
     * @access public
     * @param
     * @return mixed
     */
    public function menu($operate = '')
    {
        $tpl = $operate ? 'menu_' . $operate : 'menu';
        return $this->fetch($tpl);
    }

    /**
     * 设置
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function config()
    {
        return $this->fetch();
    }
}
