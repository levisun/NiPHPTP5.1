<?php
/**
 *
 * 数据层
 * 文章表
 *
 * @package   NiPHP
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

class Article extends Model
{
    use SoftDelete;
    protected $name = 'article';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'category_id'   => 'integer',
        'type_id'       => 'integer',
        'is_pass'       => 'integer',
        'is_com'        => 'integer',
        'is_top'        => 'integer',
        'is_hot'        => 'integer',
        'sort'          => 'integer',
        'hits'          => 'integer',
        'user_id'       => 'integer',
        'is_link'       => 'integer',
    ];
    protected $field = [
        'id',
        'title',
        'keywords',
        'description',
        'content',
        'thumb',
        'category_id',
        'type_id',
        'is_pass',
        'is_com',
        'is_top',
        'is_hot',
        'sort',
        'hits',
        'username',
        'origin',
        'user_id',
        'url',
        'is_link',
        'show_time',
        'create_time',
        'update_time',
        'delete_time',
        'access_id',
        'lang'
    ];

    /**
     * 获取器
     * 审核名称
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getPassNameAttr($_value, $_data)
    {
        return lang('pass ' . $_data['is_pass']);
    }

    /**
     * 获取器
     * 栏目模型名
     * @access protected
     * @param  string $_value
     * @return string
     */
    protected function getModelNameAttr($_value)
    {
        return lang('model ' . $_value);
    }

    /**
     * 获取器
     * 推荐状态名
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getComNameAttr($_value, $_data)
    {
        return lang('article com ' . $_data['is_com']);
    }

    /**
     * 获取器
     * 最热状态名
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getHotNameAttr($_value, $_data)
    {
        return lang('article hot ' . $_data['is_hot']);
    }

    /**
     * 获取器
     * 置顶状态名
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getTopNameAttr($_value, $_data)
    {
        return lang('article top ' . $_data['is_top']);
    }

    /**
     * 获取器
     * 跳转状态名
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getLinkNameAttr($_value, $_data)
    {
        return lang('article link ' . $_data['is_link']);
    }
}
