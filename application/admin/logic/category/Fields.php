<?php
/**
 *
 * 自定义字段 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Fields.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\category;

use app\common\model\Fields as ModelFields;
use app\common\model\Category as ModelCategory;

class Fields extends ModelFields
{

    /**
     * 查询自定义字段数据
     * @access public
     * @param  array  $_request_data 请求参数
     * @return array
     */
    public function getListData($_request_data)
    {
        $map = [];
        // 搜索
        if ($_request_data['key']) {
            $map[] = ['f.name', 'like', $_request_data['key'] . '%'];
        }
        // 安栏目
        if ($_request_data['cid']) {
            $map[] = ['f.category_id', '=', $_request_data['cid']];
        }
        // 安模型
        if ($_request_data['mid']) {
            $map[] = ['m.id', '=', $_request_data['mid']];
        }

        // $model_fields = new ModelFields;
        $result =
        $this->view('fields f', 'id,category_id,name,description,is_require')
        ->view('category c', ['name'=>'cat_name'], 'c.id=f.category_id')
        ->view('fields_type t', ['name'=>'type_name'], 't.id=f.type_id')
        ->where($map)
        ->order('f.id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->require = $value->require;
            $result[$key]->url = $value->operation_url;
        }

        return [
            'data' => $result->toArray(),
            'page' => $result->render(),
        ];
    }

    /**
     * 获得导航
     * @access public
     * @param  int    $_pid
     * @return array
     */
    public function getCategory($_pid = 0)
    {
        $map = [
            ['pid', '=', $_pid],
            ['model_id', 'not in', '8,9'],
            ['lang', '=', lang(':detect')],
        ];

        $model_category = new ModelCategory;
        $result =
        $model_category->field(true)
        ->where($map)
        ->select();

        $return = $result->toArray();
        foreach ($return as $key => $value) {
            $result = $this->getCategory($value['id']);
            $return[$key]['child'] = $result;
        }

        return $return;
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
            ['id', '=', $_request_data['id']],
        ];

        $model_fields = new ModelFields;
        $result =
        $this->field(true)
        ->where($map)
        ->find();

        return $result ? $result->toArray() : [];
    }

    /**
     * 删除
     * @access public
     * @param  array  $_request_data
     * @return boolean
     */
    public function remove($_request_data)
    {
        $map = [
            ['f.id', '=', $_request_data['id']],
        ];

        // $model_fields = new ModelFields;
        $result =
        $this->view('fields f', [])
        ->view('category c', [], 'c.id=f.category_id')
        ->view('model m', ['table_name'], 'm.id=c.model_id')
        ->where($map)
        ->find();

        $result = $result ? $result->toArray() : [];
        $table_name = ucfirst($result['table_name'] . 'Data');

        $table_model = model($table_name, 'logic');
        $table_model->remove($_request_data);

        $return = parent::remove($_request_data);
    }
}
