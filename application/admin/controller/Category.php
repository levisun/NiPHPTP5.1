<?php
/**
 *
 * 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Category extends Base
{

    /**
     * 管理栏目
     * @access public
     * @param
     * @return mixed
     */
    public function category($operate = '')
    {
        $this->assign('button_search', true);
        $this->assign('button_added', true);

        if ($operate == 'added') {
            $tpl = $this->added('Category/added', 'controller\category');
        } elseif ($operate == 'editor') {
            $tpl = $this->editor('Category/editor', 'controller\category');
        } elseif ($operate == 'remove') {
            $this->remove('Category/remove', 'controller\category');
        } elseif ($operate == 'sort') {
            $this->sort('Category/sort', 'controller\category');
        } else {
            $tpl = $this->listing('Category/getListData', 'controller\category');
        }

        return $tpl;
    }

    public function model($operate = '')
    {
        $this->assign('button_added', true);

        if ($operate == 'added') {
            $tpl = $this->added('Model/added', 'controller\category');
        } elseif ($operate == 'editor') {
            $tpl = $this->editor('Model/editor', 'controller\category');
        } elseif ($operate == 'remove') {
            $this->remove('Model/remove', 'controller\category');
        } elseif ($operate == 'sort') {
            $this->sort('Model/sort', 'controller\category');
        } else {
            $tpl = $this->listing('Model/getListData', 'controller\category');
        }

        return $tpl;
    }
}
