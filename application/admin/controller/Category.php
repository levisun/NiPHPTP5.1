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

use app\admin\logic\category\Category as LogicCategory;

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

        $logic_category = new LogicCategory;

        switch ($operate) {
            case 'added':
                if ($this->request->isPost()) {
                    $result = $logic_category->added();
                    $this->showMessage($result, lang('added success'));
                } else {
                    $result = [
                        'parent' => $logic_category->getParentData(),
                        'type'   => $logic_category->getCategoryType(),
                        'models' => $logic_category->getModelsOpen(),
                        'level'  => $logic_category->getLevelOpen(),
                    ];
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('category_added');
                }
                break;

            case 'remove':
                $id = input('param.id/f');
                $result = $logic_category->remove($id);
                $this->showMessage($result, lang('remove success'));
                break;

            case 'editor':
                if ($this->request->isPost()) {
                    $result = $logic_category->update();
                    $this->showMessage($result, lang('update success'));
                } else {
                    $result = [
                        'data'   => $logic_category->getEditorData(),
                        'type'   => $logic_category->getCategoryType(),
                        'models' => $logic_category->getModelsOpen(),
                        'level'  => $logic_category->getLevelOpen(),
                    ];
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('category_editor');
                }
                break;

            case 'sort':
                $result = $logic_category->sort();
                if ($result) {
                    $this->showMessage($result, lang('sort success'));
                } else {
                    $this->showMessage($result, lang('sort filt'));
                }
                break;

            default:
                $result = $logic_category->select();
                $this->assign('json_data', json_encode($result));
                $tpl = $this->fetch();
                break;
        }

        return $tpl;
    }
}
