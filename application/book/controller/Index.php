<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\book\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\book\controller;

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
        // $this->assign('data', logic('book/listing')->query());
        return $this->fetch('list');
    }

    /**
     * 内容
     * @access public
     * @param
     * @return mixed
     */
    public function article()
    {
        // $this->assign('data', logic('book/article')->query());
        return $this->fetch('article');
    }

    public function caiji($_url = '/wapbook/4301_2505622.html', $_num = 0)
    {
    }
}
