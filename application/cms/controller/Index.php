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
        return $this->fetch('index');
    }

    /**
     * 列表页
     * @access public
     * @param
     * @return mixed
     */
    public function entry()
    {
        $table_name = logic('cms/article')->queryTableName();
        if (!$table_name) {
            abort(404);
        }
        return $this->fetch('list_' . $table_name);
    }

    /**
     * 内容
     * @access public
     * @param
     * @return mixed
     */
    public function article()
    {
        $table_name = logic('cms/article')->queryTableName();
        if (!$table_name) {
            abort(404);
        }
        return $this->fetch($table_name);
    }

    /**
     * 频道页
     * @access public
     * @param
     * @return mixed
     */
    public function channel()
    {
        return $this->fetch('channel');
    }

    /**
     * 反馈
     * @access public
     * @param
     * @return mixed
     */
    public function feedback()
    {
        $this->assign('data', logic('cms/feedback')->queryInput());
        return $this->fetch('feedback');
    }

    /**
     * 留言
     * @access public
     * @param
     * @return mixed
     */
    public function message()
    {
        return $this->fetch('message');
    }

    /**
     * 评论
     * @access public
     * @param
     * @return mixed
     */
    public function comment()
    {
        return $this->fetch('comment');
    }

    /**
     * 标签
     * @access public
     * @param
     * @return mixed
     */
    public function tags()
    {
        return $this->fetch('tags');
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

    /**
     * 异常抛出
     * @access public
     * @param
     * @return void
     */
    public function abort()
    {
        abort(input('param.code/f', 404));
        return false;
    }
}
