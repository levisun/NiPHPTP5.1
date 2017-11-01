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

        switch ($operate) {
            case 'added':
            case 'editor':
                $result = action('Category/' . $operate, [], 'category');
                if (is_string($result)) {
                    $this->showMessage($result, lang($operate . ' success'));
                } else {
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('category_added');
                }
                break;

            case 'remove':
            case 'sort':
                $result = action('Category/' . $operate, [], 'category');
                $this->showMessage($result, lang($operate .' success'));
                break;

            default:
                $result = action('Category/getListData', [], 'category');
                $this->assign('json_data', json_encode($result));
                $tpl = $this->fetch();
                break;
        }

        return $tpl;
    }

    /**
     * 管理模型
     * @access public
     * @param
     * @return mixed
     */
    public function model($operate = '')
    {
        $this->assign('button_added', true);

        if ($operate == 'added') {
            $tpl = $this->added('Model/added');
        } elseif ($operate == 'editor') {
            $tpl = $this->editor('Model/editor');
        } elseif ($operate == 'remove') {
            $this->remove('Model/remove');
        } elseif ($operate == 'sort') {
            $this->sort('Model/sort');
        } else {
            $tpl = $this->listing('Model/getListData');
        }

        return $tpl;
    }

    /**
     * 自定义字段
     * @access public
     * @param
     * @return mixed
     */
    public function fields($operate = '')
    {
        $this->assign('button_search', true);
        $this->assign('button_added', true);

        if ($operate == 'added') {
            $tpl = $this->added('Fields/added');
        } elseif ($operate == 'editor') {
            $tpl = $this->editor('Fields/editor');
        } elseif ($operate == 'remove') {
            $this->remove('Fields/remove');
        } elseif ($operate == 'sort') {
            $this->sort('Fields/sort');
        } else {
            $tpl = $this->listing('Fields/getListData');
        }

        return $tpl;
    }
}
