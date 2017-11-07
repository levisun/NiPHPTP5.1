<?php
/**
 *
 * 自定义字段 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Fields.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\category;

use app\admin\logic\category\Fields as LogicFields;

use app\common\logic\FieldsType as LogicFieldsType;

class Fields
{

    /**
     * 获得列表数据
     * @access public
     * @param
     * @return array
     */
    public function getListData()
    {
        $request_data = [
            'key'  => input('param.q'),
            'cid'  => input('param.cid/f'),
            'mid'  => input('param.mid/f'),
        ];

        $logic_fields = new LogicFields;
        return $logic_fields->getListData($request_data);
    }

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        if (request()->isPost()) {
            $form_data = [
                'name'        => input('post.name'),
                'type_id'     => input('post.type_id/f'),
                'category_id' => input('post.category_id/f'),
                'is_require'  => input('post.is_require/f'),
                'description' => input('post.description'),
                '__token__'   => input('post.__token__'),
            ];
            $return = validate($form_data, 'Fields.create', '', 'common');
            if (true === $return) {
                unset($form_data['__token__']);

                $logic_category = new LogicFields;
                $return = $logic_category->create($form_data);
            }
        } else {
            $logic_fields = new LogicFields;
            $logic_fields_type = new LogicFieldsType;
            $return = [
                'category_list' => $logic_fields->getCategory(),
                'fields_type'   => $logic_fields_type->getOpen(),
            ];
        }

        return $return;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        if (request()->isPost()) {
            $form_data = [
                'id'          => input('post.id/f'),
                'name'        => input('post.name'),
                'type_id'     => input('post.type_id/f'),
                'category_id' => input('post.category_id/f'),
                'is_require'  => input('post.is_require/f'),
                'description' => input('post.description'),
                '__token__'   => input('post.__token__'),
            ];
            $return = validate($form_data, 'Fields.update', '', 'common');
            if (true === $return) {
                unset($form_data['__token__']);

                $logic_category = new LogicFields;
                $return = $logic_category->update($form_data);
            }
        } else {
            $request_data = [
                'id'  => input('param.id/f'),
            ];
            $logic_fields = new LogicFields;
            $logic_fields_type = new LogicFieldsType;
            $return = [
                'data'          => $logic_fields->getEditorData($request_data),
                'category_list' => $logic_fields->getCategory(),
                'fields_type'   => $logic_fields_type->getOpen(),
            ];
        }

        return $return;
    }

    /**
     * 删除
     */
    public function remove()
    {
        $request_data = [
            'id'  => input('param.id/f'),
        ];

        $logic_fields = new LogicFields;
        return $logic_fields->remove($request_data);
    }
}
