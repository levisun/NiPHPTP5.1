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
    public function category($method = '')
    {
        $this->assign('button_search', true);
        $this->assign('button_added', true);

        if ($method == 'added') {
            $result = action('Category/addedCategory', [], 'controller\category');
            if (!is_array($result)) {
                # code...
            } else {
                $this->assign('json_data_category', json_encode($result));
                return $this->fetch('category_added');
            }
        } elseif ($method == 'editor') {
            $result = action('Category/editorCategory', [], 'controller\category');
            if (!is_array($result)) {
                $this->showMessage($result, lang('editor success'));
            } else {
                $this->assign('json_data_category', json_encode($result));
                return $this->fetch('category_editor');
            }
        }

        $result = action('Category/getListData', [], 'controller\category');
        $this->assign('json_data_category', json_encode($result));
        return $this->fetch();
    }
}
