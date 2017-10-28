<?php
/**
 *
 * 自定义字段表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Fields.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class Fields extends Model
{
    protected $name = 'fields';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'category_id',
        'type_id',
        'name',
        'description',
        'is_require',
    ];

    /**
     * 获取器
     * 操作url
     * @access public
     * @param
     * @return string
     */
    public function getOperationUrlAttr($_value, $_data)
    {
        $url = [
            'editor' => url('', array('operate' => 'editor', 'id' => $_data['id'])),
            'remove' => url('', array('operate' => 'remove', 'id' => $_data['id'])),
        ];

        return $url;
    }

    /**
     * 获取器
     * 栏目模型名
     * @access public
     * @param  string $_value
     * @return string
     */
    public function getModelNameAttr($_value)
    {
        return lang('model ' . $_value);
    }
}
