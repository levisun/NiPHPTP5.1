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
 * @since     2017/12
 */
namespace app\admin\logic\category;

use think\facade\Env;

class Model
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/models')->field(true)
        ->order('id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->model_status = $value->model_status;
            $result[$key]->model_name   = $value->model_name;

            if ($value->id > 9) {
                $result[$key]->url = [
                    'editor' => url('category/model', ['operate' => 'editor', 'id' => $value['id']]),
                    'remove' => url('category/model', ['operate' => 'remove', 'id' => $value['id']]),
                ];
            }
        }
        $page = $result->render();
        $list = $result->toArray();

        return [
            'data' => $list['data'],
            'page' => $page
        ];
    }
}
