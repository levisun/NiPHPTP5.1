<?php
/**
 *
 * 自定义字段 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
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
        ->paginate(null, null, [
            'path' => url('category/fields'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->require = $value->require;

            $result[$key]->url = [
                'editor' => url('category/fields', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('category/fields', ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }
        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
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
        $map = [
            ['pid', '=', $_pid],
            ['model_id', 'not in', '8,9'],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')->field(['id', 'name'])
        ->where($map)
        ->order('sort DESC, id DESC')
        ->select();

        foreach ($result as $key => $value) {
            $res = $this->category($value->id);
            $result[$key]->child = $res;
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
        model('common/FieldsType')->field(['id', 'name'])
        ->order('id ASC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->field_name = $value->fieldName;
        }

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

        $result = validate('admin/fields.added', $receive_data, 'category');
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/fields')
        ->added($receive_data);

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
        $receive_data = [
            'id'  => input('post.id/f'),
        ];

        return model('common/fields')
        ->remove($receive_data);
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {
        $map = [
            ['id', '=', input('post.id/f')]
        ];

        return model('common/fields')->field(true)
        ->where($map)
        ->find();
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

        $result = validate('admin/fields.editor', $receive_data, 'category');

        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        return model('common/fields')
        ->editor($receive_data);
    }
}
