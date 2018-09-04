<?php
/**
 *
 * 内容 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/7
 */
namespace app\admin\logic\content;

class Content
{

    /**
     * 内容类别
     * @access public
     * @param
     * @return array
     */
    public function category()
    {
        $map = [
            ['c.pid', '=', input('param.pid/f', 0)],
            ['c.model_id', '<>', '9'],
            ['c.lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->view('category c', ['id', 'name', 'type_id', 'is_show', 'is_channel', 'model_id'])
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

            $url = [];

            if ($value->child) {
                $url['child'] = url('content/content', array('operate' => 'child', 'pid' => $value->id));
            }

            if ($value->model_id == 4) {
                $url['manage'] = url('content/content', array('operate' => 'page', 'cid' => $value->id));
            } else {
                $url['manage'] = url('content/content', array('operate' => 'manage', 'cid' => $value->id));
            }

            $result[$key]->url = $url;
        }

        return $result->toArray();
    }

    public function query()
    {
        // 查找栏目所属模型
        $result =
        model('common/category')
        ->view('category c', ['id', 'name'])
        ->view('model m', ['name' => 'model_tablename'], 'm.id=c.model_id')
        ->where([
            ['c.id', '=', input('param.cid')],
        ])
        ->find();
        $result = $result->toArray();
        $table_name = $result['model_tablename'];

        // 查询数据
        $fields = ['id', 'title', 'sort', 'is_pass', 'update_time', 'create_time'];
        if ($table_name !== 'link') {
            $fields[] = 'is_com';
            $fields[] = 'is_hot';
            $fields[] = 'is_top';
            $fields[] = 'is_link';
        }
        $result =
        model('common/' . $table_name)
        ->view($table_name . ' a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->where([
            ['a.category_id', '=', input('param.cid')]
        ])
        ->order('a.id DESC')
        ->paginate(null, null, [
            'path' => url('content/content', ['operate' => 'manage', 'cid' => input('param.cid')]),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->pass_name = $value->is_pass;

            if ($table_name !== 'link') {
                // 查询自定义字段
                $fields =
                model('common/' . $table_name . 'Data')
                ->view($table_name . '_data d', 'data')
                ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
                ->where([
                    ['d.main_id', '=', $value->id],
                ])
                ->select();
                $fields = $fields->toArray();
                foreach ($fields as $val) {
                    $result[$key]->$val['fields_name'] = $val['data'];
                }
            }
        }

        $page = $result->render();
        $list = $result->toArray();

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $page
        ];
    }
}
