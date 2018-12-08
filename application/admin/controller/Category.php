<?php
/**
 *
 * 栏目 - 控制器
 *
 * @package   NiPHP
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

class Category extends Base
{

    /**
     * 管理栏目
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function category($operate = '')
    {
        $tpl = $operate ? 'category_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 管理模型
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function model($operate = '')
    {
        $tpl = $operate ? 'model_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 管理自定义字段
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function fields($operate = '')
    {
        $this->assign('button_search', 1);

        $tpl = $operate ? 'fields_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 管理分类
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function type($operate = '')
    {
        $this->assign('button_search', 1);

        $tpl = $operate ? 'type_' . $operate : '';
        return $this->fetch($tpl);
    }
}
