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

        $fields = logic('Fields', 'category', 'admin');
        return $fields->getListData($request_data);
    }

    public function added()
    {
        if (request()->isPost()) {

        } else {
            $return = [
                'fields_type' => logic('FieldsType', '', 'common')->getOpen(),
            ];
        }

        return $return;
    }
}
