<?php
/**
 *
 * 分类 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\category;

class Type
{

    public function query()
    {
        $map = [];
        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['t.name', 'like', $key . '%'];
        }
        // 安栏目
        if ($cid = input('param.cid/f')) {
            $map[] = ['t.category_id', '=', $cid];
        }

        $result =
        model('common/type')
        ->view('type t', 'id,category_id,name,description')
        ->view('category c', ['name'=>'cat_name'], 'c.id=t.category_id')
        ->where($map)
        ->order('t.id DESC')
        ->paginate(null, null, [
            'path' => url('category/type'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('category/type', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('category/type', ['operate' => 'remove', 'id' => $value['id']]),
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
