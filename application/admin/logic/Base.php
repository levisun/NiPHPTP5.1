<?php
/**
 *
 * 全局 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Base.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic;

use think\facade\Session;

class Base
{
    public function __construct()
    {
        # code...
    }

    protected function added($added_data, $model)
    {
        $model->data($added_data)
        ->allowField(true)
        ->isUpdate(false)
        ->save();

        return $model->id ? true : false;
    }

    protected function remove($map, $model)
    {
        $result =
        $model->field(true)
        ->where($map)
        ->value('id', 0);

        if (!$result) {
            return false;
        }

        $result =
        $model->where($map)
        ->delete();

        return $result ? true : false;
    }

    protected function phyRemove($map, $model)
    {
        $result =
        $model->field(true)
        ->where($map)
        ->value('id', 0);

        if (!$result) {
            return false;
        }

        $result =
        $model->onlyTrashed()
        ->where($map)
        ->delete();

        return $result ? true : false;
    }

    protected function update($update_data, $map, $model)
    {
        $result =
        $model->allowField(true)
        ->isUpdate(true)
        ->save($update_data, $map);

        return $result ? true : false;
    }
}
