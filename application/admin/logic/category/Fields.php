<?php
/**
 *
 * 自定义字段 - 栏目 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\category;

class Fields
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $map = [];
        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['f.name', 'like', $key . '%'];
        }
        // 安栏目
        if ($cid = input('param.cid/f')) {
            $map[] = ['f.category_id', '=', $cid];
        }
        // 安模型
        if ($mid = input('param.mid/f')) {
            $map[] = ['m.id', '=', $mid];
        }

        $result =
        model('common/fields')
        ->view('fields f', 'id,category_id,name,description,is_require')
        ->view('category c', ['name'=>'cat_name'], 'c.id=f.category_id')
        ->view('fields_type t', ['name'=>'type_name'], 't.id=f.type_id')
        ->where($map)
        ->order('f.id DESC')
        ->append([
            'require'
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('category/fields', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('category/fields', ['operate' => 'remove', 'id' => $value->id]),
            ];
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

    /**
     * 获得导航
     * @access public
     * @param
     * @return array
     */
    public function category($_pid = 0)
    {
        $result =
        model('common/category')
        ->field(['id', 'name'])
        ->where([
            ['pid', '=', $_pid],
            ['model_id', 'not in', '8,9'],
            ['lang', '=', lang(':detect')],
        ])
        ->order('sort DESC, id DESC')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $res = $this->category($value['id']);
            $result[$key]['child'] = $res;
        }

        return $result;
    }

    /**
     * 获得字段类型
     * @access public
     * @param
     * @return array
     */
    public function type()
    {
        $result =
        model('common/FieldsType')
        ->field(['id', 'name'])
        ->order('id ASC')
        ->append([
            'field_name'
        ])
        ->select()
        ->toArray();

        return $result;
    }

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'name'        => input('post.name'),
            'type_id'     => input('post.type_id/f'),
            'category_id' => input('post.category_id/f'),
            'is_require'  => input('post.is_require/f', 1),
            'description' => input('post.description'),
            '__token__'   => input('post.__token__'),
        ];

        $result = validate('admin/category/fields.added', input('post.'));
        if (true !== $result) {
            return $result;
        }

        $result =
        model('common/fields')
        ->added($receive_data);

        create_action_log($receive_data['name'], 'fields_added');

        return !!$result;
    }

    /**
     * 删除
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {
        $result =
        model('common/fields')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')],
        ])
        ->find()
        ->toArray();

        create_action_log($result['name'], 'fields_remove');

        return
        model('common/fields')
        ->remove([
            'id' => input('post.id/f'),
        ]);
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {
        return
        model('common/fields')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'id'          => input('post.id/f'),
            'name'        => input('post.name'),
            'type_id'     => input('post.type_id/f'),
            'category_id' => input('post.category_id/f'),
            'is_require'  => input('post.is_require/f', 1),
            'description' => input('post.description'),
            '__token__'   => input('post.__token__'),
        ];

        $result = validate('admin/category/fields.editor', input('post.'));

        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['name'], 'fields_editor');

        return
        model('common/fields')
        ->editor($receive_data);
    }
}
