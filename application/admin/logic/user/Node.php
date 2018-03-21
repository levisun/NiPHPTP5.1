<?php
/**
 *
 * 系统节点 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

class Node
{

    /**
     * 查询
     * @access public
     * @param
     * @return mixed
     */
    public function query()
    {
        $result =
        model('common/node')->field(true)
        ->order('sort ASC, id ASC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->level_name = $value->level_name;
            $result[$key]->url = [
                'editor' => url('user/node', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('user/node', ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }

        return node_format($result);
    }

    /**
     *
     */
    public function added()
    {
        # code...
    }
}


