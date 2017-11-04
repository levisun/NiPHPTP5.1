<?php
/**
 *
 * 字段类型表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: FieldsType.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class FieldsType extends Model
{
    protected $name = 'fields_type';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'name',
        'description',
        'regex'
    ];

    /**
     * 获取器
     * 栏目模型名
     * @access public
     * @param  string $_value
     * @return string
     */
    public function getFieldNameAttr($_value, $_data)
    {
        return lang('fields type ' . $_data['name']);
    }
}
