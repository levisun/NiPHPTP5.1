<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index extends Base
{
    /**
     * 首页
     * @access public
     * @param
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('index.html');
    }

    /**
     * 列表页
     * @access public
     * @param
     * @return mixed
     */
    public function entry($operate = '')
    {
        $tpl = $operate ? $operate : '';
        return $this->fetch($operate . '.html');
    }

    /**
     * 频道页
     * @access public
     * @param
     * @return mixed
     */
    public function channel()
    {
        halt('channel');
        return $this->fetch('channel.html');
    }

    /**
     * 反馈
     * @access public
     * @param
     * @return mixed
     */
    public function feedback()
    {
        halt('feedback');
        return $this->fetch('feedback.html');
    }

    /**
     * 留言
     * @access public
     * @param
     * @return mixed
     */
    public function message()
    {
        halt('message');
        return $this->fetch('message.html');
    }
}
