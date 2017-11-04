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

use app\admin\logic\category\Model as LogicModel;

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
        $logic_model = new LogicModel;
        return $logic_model->getListData();
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
            $form_data = [
                'name'        => input('post.name'),
                'table_name'  => input('post.table_name'),
                'status'      => input('post.status/f', 0),
                'remark'      => input('post.remark'),
                'model_table' => input('post.model_table'),
                '__token__'   => input('post.__token__'),
            ];
            // 验证请求数据
            $return = validate($form_data, 'Model.create', 'category', 'admin');
            if (true === $return) {
                unset($form_data['__token__']);

                $logic_model = new LogicModel;
                $return = $logic_model->create($form_data);
            }
        } else {
            $logic_model = new LogicModel;
            $return = [
                'models' => $logic_model->getSysModel(),
            ];
        }

        return $return;
    }

    /**
     * 编辑栏目
     * @access public
     * @param
     * @return array
     */
    public function editor()
    {
        if (request()->isPost()) {
            $form_data = [
                'id'          => input('post.id/f'),
                'name'        => input('post.name'),
                'table_name'  => input('post.table_name'),
                'status'      => input('post.status/f', 0),
                'remark'      => input('post.remark'),
                '__token__'   => input('post.__token__'),
            ];
            $return = validate($form_data, 'Model.editor', 'category', 'admin');
            if (true === $return) {
                unset($form_data['__token__']);

                $logic_model = new LogicModel;
                $return = $logic_model->update($form_data);
            }
        } else {
            $request_data = [
                'id'  => input('param.id/f'),
            ];

            $logic_model = new LogicModel;
            $return = $logic_model->getEditorData($request_data);

            $return = [
                'data'  => $logic_model->getEditorData($request_data),
                'models' => $logic_model->getSysModel(),
            ];
        }

        return $return;
    }

    /**
     * 删除模型
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {
        $request_data = [
            'id'  => input('param.id/f'),
        ];

        $logic_model = new LogicModel;
        return $logic_model->remove($request_data);
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $form_data = [
            'id' => input('post.sort/a'),
        ];

        $logic_model = new LogicModel;
        return $logic_model->sort($form_data);
    }
}
