<?php
/**
 *
 * 管理模型 - 栏目 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\category;

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
        model('common/models')
        ->field(true)
        ->order('id DESC')
        ->append([
            'model_status',
            'model_name'
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            if ($value->id > 9) {
                $result[$key]->url = [
                    'editor' => url('category/model', ['operate' => 'editor', 'id' => $value->id]),
                    'remove' => url('category/model', ['operate' => 'remove', 'id' => $value->id]),
                ];
            }
        }

        $list = $result->toArray();

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
        ];
    }
}
