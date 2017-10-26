<?php
/**
 *
 * 管理模型 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Model.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\category;

class Model
{

    /**
     * 获得列表数据
     * @access public
     * @param
     * @return array
     */
    public function getListData()
    {
        $model = logic('Model', 'logic\category');
        return $model->getListData();
    }

    /**
     * 获得添加所需数据
     * @access public
     * @param
     * @return array
     */
    public function added()
    {
        if (request()->isPost()) {
            # code...
        } else {
            $model = logic('Model', 'logic\category');
            $return = $model->getModels();
            halt($return);
        }

        return $return;
    }
}
