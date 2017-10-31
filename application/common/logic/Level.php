<?php
/**
 *
 * 会员等级 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Level.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\Level as ModelLevel;

class Level
{

    /**
     * 新增会员等级
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function create($_form_data)
    {
        $level = new ModelLevel;
        $result =
        $level->allowField(true)
        ->create($_form_data);

        return $result->id;
    }

    /**
     * 删除会员等级
     * @access public
     * @param  array  $_receive_data
     * @return boolean
     */
    public function remove($_receive_data)
    {
        $map  = [
            ['id', '=', $_receive_data['id']],
        ];

        $level = new ModelLevel;
        $result =
        $level->where($map)
        ->delete();

        return !!$result;
    }

    /**
     * 保存修改会员等级
     * @access public
     * @param  array  $_form_data
     * @return boolean
     */
    public function update($_form_data)
    {
        $map  = [
            ['id', '=', $_form_data['id']],
        ];

        unset($_form_data['id']);

        $level = new ModelLevel;
        $result =
        $level->allowField(true)
        ->where($map)
        ->update($_form_data);

        return !!$result;
    }

    /**
     * 获得开启的会员等级
     * @access public
     * @param
     * @return array
     */
    public function getOpen()
    {
        $map = [
            ['status', '=', 1],
        ];

        $level = new ModelLevel;
        $result =
        $level->field(true)
        ->where($map)
        ->select();

        /*foreach ($result as $key => $value) {
            $result[$key]->model_name = $value->model_name;
        }*/

        return $result->toArray();
    }
}
