<?php
/**
 *
 * 单页表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Page.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class Page extends Model
{
    protected $name = 'page';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
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
        'comment_count',
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
     * 新增
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function added($_form_data)
    {
        unset($_form_data['id'], $_form_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_form_data);

        return $result->id;
    }

    /**
     * 删除
     * @access public
     * @param  array  $_receive_data
     * @return boolean
     */
    public function remove($_receive_data)
    {
        $map  = [
            ['id', '=', $_receive_data['id']],
        ];

        $result =
        $this->where($map)
        ->delete();

        return !!$result;
    }

    /**
     * 修改
     * @access public
     * @param  array  $_form_data
     * @return boolean
     */
    public function editor($_form_data)
    {
        $map  = [
            ['id', '=', $_form_data['id']],
        ];

        unset($_form_data['id'], $_form_data['__token__']);

        $result =
        $this->allowField(true)
        ->where($map)
        ->update($_form_data);

        return !!$result;
    }
}
