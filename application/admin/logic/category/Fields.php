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

class Fields
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

        $fields = model('Fields');
        $result =
        $fields->view('fields f', 'id,category_id,name,description,is_require')
        ->view('category c', ['name'=>'cat_name'], 'c.id=f.category_id')
        ->view('fields_type t', ['name'=>'type_name'], 't.id=f.type_id')
        ->view('model m', ['name'=>'model_name'], 'm.id=c.model_id')
        ->where($map)
        ->order('f.id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = $value->operation_url;
        }

        return [
            'data' => $result->toArray(),
            'page' => $result->render(),
        ];
    }
}
