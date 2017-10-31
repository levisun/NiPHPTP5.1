<?php
/**
 *
 * 自定义字段类型 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: FieldsType.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\FieldsType as ModelFieldsType;

class FieldsType
{

    public function getList()
    {
        $fields_type = new ModelFieldsType;
        $result =
        $fields_type->field(true)
        ->where($map)
        ->order('id DESC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->field_name = $value->field_name;
        }

        return $result->toArray();
    }
}
