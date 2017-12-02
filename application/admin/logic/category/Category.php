<?php
/**
 *
 * 管理栏目 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\category;

use app\common\model\Category as ModelCategory;
use app\common\model\Models as ModelModels;
use app\common\model\Level as ModelLevel;

class Category
{

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $form_data = [
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

        $return = validate($form_data, 'Category.added', 'category', 'admin');
        if (true === $return) {
            unset($form_data['__token__']);

            $model_category = new ModelCategory;
            $return = !!$model_category->added($form_data);
        }

        return $return;
    }

    /**
     * 删除
     * @access public
     * @param  int     $_id
     * @return boolean
     */
    public function remove($_id)
    {
        // 查询子栏目
        $map  = [
            ['pid', '=', $_id],
        ];

        $model_category = new ModelCategory;

        $result =
        $model_category->field(true)
        ->where($map)
        ->find();

        // 子栏目存在 递归删除子栏目
        if ($result) {
            $params = [
                'id'  => $result['id'],
            ];

            $this->remove($params);
        }

        return $model_category->remove(['id' => $_id]);
    }

    /**
     * 修改
     * @access public
     * @param
     * @return mixed
     */
    public function update()
    {
        $form_data = [
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
        $return = validate($form_data, 'Category.update', 'category', 'admin');
        if (true === $return) {
            unset($form_data['__token__']);
            $model_category = new ModelCategory;
            $return = $model_category->update($form_data);
        }

        return $return;
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $form_data = [
            'id' => input('post.sort/a'),
        ];

        $model_category = new ModelCategory;
        return $model_category->sort($form_data);
    }

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function select()
    {
        $map = [
            ['c.pid', '=', input('param.pid/f', 0)],
            ['c.lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['c.name', 'like', $key . '%'];
        }

        $model_category = new ModelCategory;

        $result =
        $model_category->view('category c', true)
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

            $url = $value->operation_url;
            if ($value->pid) {
                $url['back'] = url('');
            } else {
                $url['back'] = false;
            }

            if ($value->child) {
                $url['child'] = url('', ['pid' => $value['id']]);
            } else {
                $url['child'] = false;
            }

            $url['add_child'] = url('', ['operate' => 'added','pid' => $value['id']]);

            $result[$key]->url = $url;

        }

        return $result;
    }

    /**
     * 获得编辑数据
     * @access public
     * @param  array  $_request_data
     * @return array
     */
    public function getEditorData()
    {
        $map = [
            ['c.id', '=', input('param.id/f')],
            ['c.lang', '=', lang(':detect')],
        ];

        $model_category = new ModelCategory;

        $result =
        $model_category->view('category c', true)
        ->view('category cc', ['name'=>'parent_name'], 'c.pid=cc.id', 'LEFT')
        ->where($map)
        ->find();

        return $result;
    }

    /**
     * 获得父级数据
     * @access public
     * @param
     * @return array
     */
    public function getParentData()
    {
        $map = [
            ['id', '=', input('param.pid/f', 0)],
            ['lang', '=', lang(':detect')],
        ];

        $model_category = new ModelCategory;

        $result =
        $model_category->field(true)
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
    public function getCategoryType()
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
    public function getModelsOpen()
    {
        $map = [
            ['status', '=', 1],
        ];

        $model_models = new ModelModels;

        $result =
        $model_models->field(true)
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
    public function getLevelOpen()
    {
        $map = [
            ['status', '=', 1],
        ];

        $model_level = new ModelLevel;

        $result =
        $model_level->field(true)
        ->where($map)
        ->select();

        return $result;
    }
}
