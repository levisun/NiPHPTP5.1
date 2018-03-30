<?php
/**
 *
 * 管理员组 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

class Role
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
        model('common/role')
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url('user/role'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->status_name = $value->status_name;
            if ($value->id == 1) {
                $result[$key]->url = [
                    'editor' => '',
                    'remove' => '',
                ];
            } else {
                $result[$key]->url = [
                    'editor' => url('user/role', ['operate' => 'editor', 'id' => $value['id']]),
                    'remove' => url('user/role', ['operate' => 'remove', 'id' => $value['id']]),
                ];
            }

        }

        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
        ];
    }

}
