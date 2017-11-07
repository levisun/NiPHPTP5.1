<?php
/**
 *
 * 图文扩展表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: PictureData.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class PictureData extends Model
{
    protected $name = 'picture_data';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'main_id',
        'fields_id',
        'data'
    ];

    /**
     * 新增
     * @access protected
     * @param  array  $_form_data
     * @return mixed
     */
    protected function added($_form_data)
    {
        unset($_form_data['id'], $_form_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_form_data);

        return $result->id;
    }

    /**
     * 删除
     * @access protected
     * @param  array  $_receive_data
     * @return boolean
     */
    protected function remove($_receive_data)
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
     * @access protected
     * @param  array  $_form_data
     * @return boolean
     */
    protected function editor($_form_data)
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
