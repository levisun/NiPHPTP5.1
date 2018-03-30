<?php
/**
 *
 * 管理栏目 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\category;

use \File;

class Category
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $map = [
            ['c.pid', '=', input('param.pid/f', 0)],
            ['c.lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['c.name', 'like', $key . '%'];
        }

        $result =
        model('common/category')
        ->view('category c', true)
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('category cc', ['id' => 'child'], 'c.id=cc.pid', 'LEFT')
        ->where($map)
        ->group('c.id')
        ->order('c.sort DESC, c.id DESC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->type_name = $value->type_name;
            $result[$key]->show      = $value->show;
            $result[$key]->channel   = $value->channel;

            $url = [
                'add_child' => url('category/category', ['operate' => 'added', 'pid' => $value['id']]),
                'editor'    => url('category/category', ['operate' => 'editor', 'id' => $value['id']]),
                'remove'    => url('category/category', ['operate' => 'remove', 'id' => $value['id']]),
            ];

            if ($value->pid) {
                $url['back'] = url('category/category');
            } else {
                $url['back'] = false;
            }

            if ($value->child) {
                $url['child'] = url('category/category', ['pid' => $value['id']]);
            } else {
                $url['child'] = false;
            }

            $result[$key]->url = $url;

        }

        return $result;
    }

    /**
     * 获得父级数据
     * @access public
     * @param
     * @return array
     */
    public function parent()
    {
        if (!input('post.pid/f', 0)) {
            return ;
        }
        $map = [
            ['id', '=', input('post.pid/f', 0)],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')->field(true)
        ->where($map)
        ->find();

        return $result;
    }

    /**
     * 获得导航类型
     * @access public
     * @param
     * @return array
     */
    public function type()
    {
        return [
            ['id' => 1, 'name' => lang('type top')],
            ['id' => 2, 'name' => lang('type main')],
            ['id' => 3, 'name' => lang('type foot')],
            ['id' => 4, 'name' => lang('type other')]
        ];
    }

    /**
     * 获得开启的模型
     * @access public
     * @param
     * @return array
     */
    public function models()
    {
        $map = [
            ['status', '=', 1],
        ];

        $result =
        model('common/models')->field(['id', 'name'])
        ->where($map)
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]['model_name'] = $value->model_name;
        }

        return $result;
    }

    /**
     * 获得开启的会员等级
     * @access public
     * @param
     * @return array
     */
    public function level()
    {
        $map = [
            ['status', '=', 1],
        ];

        $result =
        model('common/level')->field(['id', 'name'])
        ->where($map)
        ->select();

        return $result;
    }

    /**
     * 新增栏目
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'name'            => input('post.name'),
            'aliases'         => input('post.aliases'),
            'pid'             => input('post.pid/f', 0),
            'type_id'         => input('post.type_id/f', 1),
            'model_id'        => input('post.model_id/f', 1),
            'is_show'         => input('post.is_show/f', 1),
            'is_channel'      => input('post.is_channel/f', 0),
            'image'           => input('post.image'),
            'seo_title'       => input('post.seo_title'),
            'seo_keywords'    => input('post.seo_keywords'),
            'seo_description' => input('post.seo_description'),
            'access_id'       => input('post.access_id/f', 0),
            'lang'            => lang(':detect'),
            '__token__'       => input('post.__token__'),
        ];

        $result = validate('admin/category.added', input('post.'), 'category');
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/category')
        ->added($receive_data);

        create_action_log($receive_data['name'], 'category_added');

        return !!$result;
    }

    /**
     * 删除分类
     * @access public
     * @param  int    $_id
     * @return mixed
     */
    public function remove($_id = 0)
    {
        $_id = $_id ? $_id : input('post.id/f');

        // 查询子栏目
        $map  = [
            ['pid', '=', $_id],
        ];

        $result =
        model('common/category')->field(true)
        ->where($map)
        ->find();

        create_action_log($result['name'], 'category_remove');

        // 子栏目存在 递归删除子栏目
        if ($result) {
            $params = [
                'id' => $result['id'],
            ];

            $this->remove($params);
        }

        // 查询当前分类信息
        $map  = [
            ['id', '=', $_id],
        ];
        $result =
        model('common/category')->field(true)
        ->where($map)
        ->find();
        // 删除图片
        File::remove(env('root_path') . basename(request()->root()) . $result['image']);

        return model('common/category')
        ->remove(['id' => $_id]);
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

        return model('common/category')->field(true)
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
            'id'              => input('post.id/f'),
            'name'            => input('post.name'),
            'aliases'         => input('post.aliases'),
            'pid'             => input('post.pid/f', 0),
            'type_id'         => input('post.type_id/f', 1),
            'model_id'        => input('post.model_id/f', 1),
            'is_show'         => input('post.is_show/f', 1),
            'is_channel'      => input('post.is_channel/f', 0),
            'image'           => input('post.image'),
            'seo_title'       => input('post.seo_title'),
            'seo_keywords'    => input('post.seo_keywords'),
            'seo_description' => input('post.seo_description'),
            'access_id'       => input('post.access_id/f', 0),
            '__token__'       => input('post.__token__'),
        ];

        $result = validate('admin/category.editor', input('post.'), 'category');
        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['name'], 'category_editor');

        return model('common/category')
        ->editor($receive_data);
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $receive_data = [
            'id' => input('post.sort/a'),
        ];

        create_action_log('', 'category_sort');

        return model('common/category')
        ->sort($receive_data);
    }
}
