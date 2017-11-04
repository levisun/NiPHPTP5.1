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

    public function added()
    {
        if (request()->isPost()) {

        } else {
            $logic_fields = new LogicFields;
            $logic_fields_type = new LogicFieldsType;
            $return = [
                'category_list' => $logic_fields->getCategory(),
                'fields_type' => $logic_fields_type->getOpen(),

            ];
        }

        return $return;
    }
}
