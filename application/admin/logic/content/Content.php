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
        ->append([
            'type_name',
            'show',
            'channel'
        ])
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $url = [];

            if ($value['child']) {
                $url['child'] = url('content/content', array('operate' => 'child', 'pid' => $value['id']));
            }

            if ($value['model_id'] == 4) {
                $url['manage'] = url('content/content', array('operate' => 'page', 'cid' => $value['id']));
            } else {
                $url['manage'] = url('content/content', array('operate' => 'manage', 'cid' => $value['id']));
            }

            $result[$key]['url'] = $url;
        }

        return $result;
    }

    /**
     * 查询内容列表
     * @access public
     * @param
     * @return [type] [description]
     */
    public function query()
    {
        // 查找栏目所属模型
        $table_name = $this->queryTableName();

        // 查询数据
        $fields = ['id', 'category_id', 'title', 'sort', 'is_pass', 'update_time', 'create_time'];
        $append = ['pass_name'];
        if ($table_name !== 'link') {
            $fields[] = 'is_com';
            $fields[] = 'is_hot';
            $fields[] = 'is_top';
            $fields[] = 'is_link';
        }

        if (!in_array($table_name, ['link', 'message', 'feedback'])) {
            $append[] = 'com_name';
            $append[] = 'hot_name';
            $append[] = 'top_name';
            $append[] = 'link_name';
        }
        $result =
        model('common/' . $table_name)
        ->view($table_name . ' a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.category_id', '=', input('param.cid/f')]
        ])
        ->order('a.id DESC')
        ->append($append)
        ->paginate(null, null, [
            'path' => url('content/content', ['operate' => 'manage', 'cid' => input('param.cid/f')]),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('content/content', ['operate' => 'editor', 'type' => $table_name, 'cid' => $value->category_id, 'id' => $value->id]),
                'remove' => url('content/content', ['operate' => 'remove', 'type' => $table_name, 'cid' => $value->category_id, 'id' => $value->id]),
            ];

            if ($table_name !== 'link') {
                // 查询自定义字段
                $fields =
                model('common/' . $table_name . 'Data')
                ->view($table_name . '_data d', 'data')
                ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
                ->where([
                    ['d.main_id', '=', $value->id],
                ])
                ->select()
                ->toArray();
                foreach ($fields as $val) {
                    $result[$key][$val['fields_name']] = $val['data'];
                }
            }
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
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        # code...
    }

    /**
     * 删除
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {}

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {
        // 查找栏目所属模型
        $table_name = $this->queryTableName();

        $result =
        model('common/' . $table_name)
        ->view($table_name . ' a', true)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.category_id', '=', input('post.cid/f')],
            ['a.id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();

        if ($table_name !== 'link') {
            // 查询自定义字段
            $fields =
            model('common/' . $table_name . 'Data')
            ->view($table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $result['id']],
            ])
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result[$val['fields_name']] = $val['data'];
            }
        }

        return $result;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {}

     /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        create_action_log('', 'category_sort');

        return
        model('common/category')
        ->sort([
            'id' => input('post.sort/a'),
        ]);
    }

    /**
     * 获取对应的模型表名
     * @access private
     * @param
     * @return string
     */
    private function queryTableName()
    {
        // 查找栏目所属模型
        $result =
        model('common/category')
        ->view('category c', ['id', 'name'])
        ->view('model m', ['name' => 'model_tablename'], 'm.id=c.model_id')
        ->where([
            ['c.id', '=', input('param.cid/f')],
        ])
        ->find();

        $result = $result->toArray();

        return $result['model_tablename'];
    }
}
