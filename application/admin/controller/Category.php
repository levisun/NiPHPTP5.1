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
            $result = action('Category/added', [], 'controller\category');
            if (!is_array($result)) {
                $this->showMessage($result, lang('added success'), 'category/category');
            } else {
                $this->assign('json_data_category', $result);
                $tpl = $this->fetch('category_added');
            }
        } elseif ($operate == 'editor') {
            $result = action('Category/editor', [], 'controller\category');
            if (!is_array($result)) {
                $this->showMessage($result, lang('editor success'));
            } else {
                $this->assign('json_data_category', json_encode($result));
                $tpl = $this->fetch('category_editor');
            }
        } elseif ($operate == 'remove') {
            $result = action('Category/remove', [], 'controller\category');
            $this->showMessage($result, lang('remove success'));
        } elseif ($operate == 'sort') {
            $result = action('Category/sort', [], 'controller\category');
            $this->showMessage($result, lang('sort success'));
        } else {
            $result = action('Category/getListData', [], 'controller\category');
            $this->assign('json_data_category', json_encode($result));
            $tpl = $this->fetch();
        }

        return $tpl;
    }

    public function model($operate = '')
    {
        $this->assign('button_added', true);

        if ($operate) {
            # code...
        } else {
            $result = action('Model/getListData', [], 'controller\category');
            halt($result);
            $this->assign('json_data_model', json_encode($result['data']));
            $tpl = $this->fetch();
        }

        return $tpl;
    }
}
