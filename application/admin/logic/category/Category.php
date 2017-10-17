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
     * @param
     * @return array
     */
    public function getListData($request_data)
    {
        $map = [
            ['c.pid', '=', $request_data['pid']],
            ['c.lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($request_data['key']) {
            $map[] = ['c.name', 'like', $request_data['key'] . '%'];
        }

        $category = model('Category');
        $result =
        $category->view('category c', 'id,pid,name,type_id,model_id,is_show,is_channel,sort')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('category cc', ['id' => 'child'], 'c.id=cc.pid', 'LEFT')
        ->where($map)
        ->group('c.id')
        ->order('c.type_id ASC, c.sort ASC, c.id DESC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]['type_name'] = $value->type_name;
            $result[$key]['show']      = $value->show;
            $result[$key]['channel']   = $value->channel;

            $url = [];
            if ($value['pid']) {
                $url['back'] = url('');
            } else {
                $url['back'] = false;
            }

            if ($value['child']) {
                $url['child'] = url('', ['pid' => $value['id']]);
            } else {
                $url['child'] = false;
            }

            $url['add_child'] = url('', ['method' => 'added','pid' => $value['id']]);
            $url['editor'] = url('', array('method' => 'editor', 'id' => $value['id']));
            $url['remove'] = url('', array('method' => 'remove', 'id' => $value['id']));

            $result[$key]['url'] = $url;

        }

        return $result;
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

        return $result;
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

        return $result;
    }

    /**
     * 获得编辑数据
     * @access public
     * @param  array  $request_data
     * @return array
     */
    public function getEditorData($request_data)
    {
        $map = [
            ['c.id', '=', $request_data['id']],
            ['c.lang', '=', lang(':detect')],
        ];

        $category = model('Category');
        $result =
        $category->view('category c', true)
        ->view('category cc', ['name'=>'parentname'], 'c.pid=cc.id', 'LEFT')
        ->where($map)
        ->find();

        return $result;
    }

    /**
     * 保存修改栏目
     * @access public
     * @param  array  $form_data
     * @return mixed
     */
    public function saveCategory($form_data)
    {
        $map  = [
            ['id', '=', $form_data['id']],
        ];

        unset($form_data['id']);

        $category = model('Category');
        $result =
        $category->where($map)
        ->update($form_data);

        return !!$result;
    }

    /**
     * 获得父级数据
     * @access public
     * @param  array  $request_data
     * @return array
     */
    public function getParentData($request_data)
    {
        $map = [
            ['id', '=', $request_data['pid']],
            ['lang', '=', lang(':detect')],
        ];

        $category = model('Category');
        $result =
        $category->field('id,name')
        ->where($map)
        ->find();

        return $result;
    }
}
