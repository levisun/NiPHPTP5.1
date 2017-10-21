<?php
/**
 *
 * 栏目表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  admin\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Models.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\model;

use think\Model;

class Models extends Model
{
    protected $name = 'model';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'name',
        'table_name',
        'remark',
        'status',
        'sort',
    ];

    /**
     * 获取器
     * 栏目类型
     * @access public
     * @param  int    $_value
     * @return string
     */
    public function getModelNameAttr($_value, $_data)
    {
        $name = [
            'article'  => lang('model article'),
            'picture'  => lang('model picture'),
            'download' => lang('model download'),
            'page'     => lang('model page'),
            'feedback' => lang('model feedback'),
            'message'  => lang('model message'),
            'product'  => lang('model product'),
            'link'     => lang('model link'),
            'external' => lang('model external'),
        ];

        return $name[$_data['name']];
    }
}
