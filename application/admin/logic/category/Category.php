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

use app\common\logic\Category as LogicCategory;

class Category extends LogicCategory
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

        $category = model('Category', '', 'common');
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

        $category = model('Category', '', 'common');
        $result =
        $category->view('category c', true)
        ->view('category cc', ['name'=>'parent_name'], 'c.pid=cc.id', 'LEFT')
        ->where($map)
        ->find();

        return $result ? $result->toArray() : [];
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

        $category = model('Category', '', 'common');
        $result =
        $category->field(true)
        ->where($map)
        ->find();

        // 子栏目存在 递归删除子栏目
        if ($result) {
            $params = [
                'id'  => $result['id'],
            ];

            $this->remove($params);
        }

        return parent::remove($_request_data);
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

        $category = model('Category', '', 'common');
        $result =
        $category->field(true)
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
}
