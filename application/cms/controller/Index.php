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
        $str = emoji_encode('大家快来动动脑筋吧😄（答案将于下期公布哟）抖音小助手 儿童文学社 #我才是实力自拍王');
        echo ($str);die();
        return $this->fetch('index.html');
    }

    /**
     * 列表页
     * @access public
     * @param
     * @return mixed
     */
    public function entry()
    {
        return $this->fetch('list_' . $this->tableName . '.html');
    }

    /**
     * 内容
     * @access public
     * @param
     * @return mixed
     */
    public function article()
    {
        return $this->fetch($this->tableName . '.html');
    }

    /**
     * 频道页
     * @access public
     * @param
     * @return mixed
     */
    public function channel()
    {
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
        return $this->fetch('message.html');
    }

    /**
     * 标签
     * @access public
     * @param
     * @return mixed
     */
    public function tags()
    {
        return $this->fetch('tags.html');
    }

    /**/
    public function go()
    {
        # code...
        die();
    }

    /**
     * IP信息
     * @access public
     * @param
     * @return mixed
     */
    public function getipinfo()
    {
        return json(logic('common/IpInfo')->getInfo());
    }
}
