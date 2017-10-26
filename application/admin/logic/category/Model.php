<?php
/**
 *
 * 管理模型 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Model.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\category;

class Model
{

    /**
     * 查询栏目数据
     * @access public
     * @param
     * @return array
     */
    public function getListData()
    {
        $model = model('Models');
        $result =
        $model->field(true)
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->model_status = $value->model_status;
            $result[$key]->model_name = $value->model_name;
            $result[$key]->url = $value->operation_url;
        }

        return [
            'data' => $result->toArray(),
            'page' => $result->render(),
        ];
    }


    public function getModels()
    {
        $map = [
            ['id', '<>', '9'],
        ];
        $model = model('Models');
        $result =
        $model->field(['id, name, table_name'])
        ->where($map)
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->model_name = $value->model_name;
        }

        return $result ? $result->toArray() : [];
    }
}
