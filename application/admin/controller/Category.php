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

use app\admin\controller\category\Category as ControllerCategory;
use app\admin\controller\category\Model as ControllerModel;
use app\admin\controller\category\Fields as ControllerFields;

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

        $controller_category = new ControllerCategory;

        switch ($operate) {
            case 'added':
            case 'editor':
                $result = $controller_category->$operate();
                if (is_string($result)) {
                    $this->showMessage($result, lang($operate . ' success'));
                } else {
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('category_' . $operate);
                }
                break;

            case 'remove':
            case 'sort':
                $result = $controller_category->$operate();
                $this->showMessage($result, lang($operate .' success'));
                break;

            default:
                $result = $controller_category->getListData();
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

        $controller_model = new ControllerModel;

        switch ($operate) {
            case 'added':
            case 'editor':
                $result = $controller_model->$operate();
                if (is_string($result)) {
                    $this->showMessage($result, lang($operate . ' success'));
                } else {
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('model_' . $operate);
                }
                break;

            case 'remove':
            case 'sort':
                $result = $controller_model->$operate();
                $this->showMessage($result, lang($operate .' success'));
                break;

            default:
                $result = $controller_model->getListData();
                $this->assign('json_data', json_encode($result));
                $tpl = $this->fetch();
                break;
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

        $controller_fields = new ControllerFields;

        switch ($operate) {
            case 'added':
            case 'editor':
                $result = $controller_fields->$operate();
                if (is_string($result)) {
                    $this->showMessage($result, lang($operate . ' success'));
                } else {
                    $this->assign('json_data', json_encode($result));
                    $tpl = $this->fetch('fields_' . $operate);
                }
                break;

            case 'remove':
            case 'sort':
                $result = $controller_fields->$operate();
                $this->showMessage($result, lang($operate .' success'));
                break;

            default:
                $result = $controller_fields->getListData();
                $this->assign('json_data', json_encode($result));
                $tpl = $this->fetch();
                break;
        }

        return $tpl;
    }
}
