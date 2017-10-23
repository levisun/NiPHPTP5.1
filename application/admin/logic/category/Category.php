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

class Category
{

    /**
     * 查询栏目数据
     * @access public
     * @param  array  $_request_data 请求参数
     * @return array
     */
    public function getListData($_request_data)
    {
        $map = [
            ['c.pid', '=', $_request_data['pid']],
            ['c.lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($_request_data['key']) {
            $map[] = ['c.name', 'like', $_request_data['key'] . '%'];
        }

        $category = model('Category');
        $result =
        $category->view('category c', true)
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

        return $result->toArray();
    }

    /**
     * 新增栏目
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function create($_form_data)
    {
        $category = model('Category');
        $result =
        $category->create($_form_data);

        return !!$result;
    }

    /**
     * 获得编辑数据
     * @access public
     * @param  array  $_request_data
     * @return array
     */
    public function getEditorData($_request_data)
    {
        $map = [
            ['c.id', '=', $_request_data['id']],
            ['c.lang', '=', lang(':detect')],
        ];

        $category = model('Category');
        $result =
        $category->view('category c', true)
        ->view('category cc', ['name'=>'parentname'], 'c.pid=cc.id', 'LEFT')
        ->where($map)
        ->find();

        return $result ? $result->toArray() : [];
    }

    /**
     * 保存修改栏目
     * @access public
     * @param  array  $_form_data
     * @return boolean
     */
    public function update($_form_data)
    {
        $map  = [
            ['id', '=', $_form_data['id']],
        ];

        unset($_form_data['id']);

        $category = model('Category');
        $result =
        $category->where($map)
        ->update($_form_data);

        return !!$result;
    }

    /**
     * 删除栏目
     * @access public
     * @param  array  $_request_data
     * @return boolean
     */
    public function remove($_request_data)
    {
        // 查询子栏目
        $map  = [
            ['pid', '=', $_request_data['id']],
        ];

        $category = model('Category');
        $result =
        $category->field('id, pid')
        ->where($map)
        ->find();

        // 子栏目存在 递归删除子栏目
        if ($result) {
            $params = [
                'id'  => $result['id'],
            ];

            $this->remove($params);
        }

        $map  = [
            ['id', '=', $_request_data['id']],
        ];
        $category->where($map)
        ->delete();

        return true;
    }

    /**
     * 排序
     * @access public
     * @param  array $_form_data
     * @return boolean
     */
    public function sort($_form_data)
    {
        foreach ($_form_data['id'] as $key => $value) {
            $data[] = [
                'id' => $key,
                'sort' => $value,
            ];
        }

        $category = model('Category');

        $result =
        $category->saveAll($data);

        return !!$result;
    }

    /**
     * 获得父级数据
     * @access public
     * @param  array  $_request_data
     * @return array
     */
    public function getParentData($_request_data)
    {
        $map = [
            ['id', '=', $_request_data['pid']],
            ['lang', '=', lang(':detect')],
        ];

        $category = model('Category');
        $result =
        $category->field('id,name')
        ->where($map)
        ->find();

        return $result ? $result->toArray() : [];
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
     * 获得模型
     * @access public
     * @param
     * @return array
     */
    public function getCategoryModels()
    {
        $map = [
            ['status', '=', 1],
        ];

        $models = model('Models');
        $result =
        $models->field('id,name,table_name')
        ->where($map)
        ->order('sort DESC')
        ->select();

        $data = [];
        foreach ($result as $key => $value) {
            $data[$key] = $value->toArray();
            $data[$key]['model_name'] = $value->model_name;
        }

        return $data;
    }

    /**
     * 获得会员等级
     * @access public
     * @param
     * @return array
     */
    public function getMemberLevel()
    {
        $map = [
            ['status', '=', 1],
        ];

        $level = model('Level');
        $result =
        $level->field('id,name')
        ->where($map)
        ->select();

        return $result ? $result->toArray() : [];
    }
}
