<?php
/**
 *
 * 关键词 - 微信 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\wechat
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\wechat;

class Keyword
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $type = input('post.type/f', 0);

        $map = [
            ['type', '=', $type],
            ['lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['keyword', 'like', $key . '%'];
        }

        $result =
        model('common/Reply')->field(true)
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url('wechat/keyword'),
        ]);

        //根据类型判断URL
        if ($type === 0) {
            $url = 'wechat/keyword';
        }

        foreach ($result as $key => $value) {
            $result[$key]->type_name = $value->type_name;
            $result[$key]->status = $value->status_name;
            $result[$key]->url = [
                'editor' => url($url, ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url($url, ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }
        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
        ];
    }
}
