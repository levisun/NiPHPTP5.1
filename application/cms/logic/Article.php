<?php
/**
 *
 * 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Article
{

    /**
     * 列表
     * @access public
     * @param
     * @return array
     */
    public function query($_cid = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');

        if (!$table_name = $this->queryTableName($_cid)) {
            return false;
        }

        // 查询数据
        $fields = ['id', 'category_id', 'title', 'sort', 'is_pass', 'is_link', 'url', 'update_time', 'create_time'];
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
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()],
            ['a.category_id', '=', $_cid]
        ])
        ->order('a.is_top, a.is_hot, a.is_com, t.name DESC, a.sort DESC, a.id DESC')
        ->append($append)
        ->cache(!APP_DEBUG)
        ->paginate(null, null, [
            'path' => url('list/'. $_cid, [], 'html', true),
        ]);

        foreach ($result as $key => $value) {
            if ($value->is_link) {
                $result[$key]->url  = url('go/' . $value->category_id . '/' . $value->id, [], 'html', true);
                $result[$key]->url .= '?go=' . urlencode($value->url);
            } else {
                $result[$key]->url = url($table_name . '/' . $value->category_id . '/' . $value->id, [], 'html', true);
            }

            $result[$key]->url = str_replace('/index/', '/', $result[$key]->url);

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
            'page'         => str_replace('/index/', '/', $result->render()),
        ];
    }

    /**
     * 列表
     * @access public
     * @param
     * @return array
     */
    public function find($_cid = 0, $_id = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');
        $_id = $_id ? (float) $_id : input('param.id/f');

        if (!$table_name = $this->queryTableName($_cid)) {
            return false;
        }

        $result =
        model('common/' . $table_name)
        ->view($table_name . ' a', true)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()],
            ['a.category_id', '=', $_cid],
            ['a.id', '=', $_id]
        ])
        ->cache(!APP_DEBUG)
        ->find();

        if ($result) {
            $result = $result->toArray();

            if ($table_name !== 'link') {
                // 查询自定义字段
                $fields =
                model('common/' . $table_name . 'Data')
                ->view($table_name . '_data d', 'data')
                ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
                ->where([
                    ['d.main_id', '=', $result['id']],
                ])
                ->cache(!APP_DEBUG)
                ->select()
                ->toArray();
                foreach ($fields as $val) {
                    $result[$val['fields_name']] = $val['data'];
                }
            }

            // 更新浏览数
            model('common/' . $table_name)
            ->where([
                ['is_pass', '=', 1],
                ['show_time', '<=', time()],
                ['category_id', '=', $_cid],
                ['id', '=', $_id]
            ])
            ->setInc('hits', APP_DEBUG ? 1 : rand(1, 10));

            if (!$this->checkAccess($result['access_id'])) {
                $result = 'not access';
            }

            return $result;
        } else {
            return false;
        }
    }

    public function go()
    {
        # code...
    }

    /**
     * 验证访问权限
     * @access private
     * @param  integer $_access_id
     * @return boolean
     */
    private function checkAccess($_access_id)
    {
        if ($_access_id != 0) {
            return false;
        }

        return true;
    }

    /**
     * 获取对应的模型表名
     * @access public
     * @param
     * @return string
     */
    public function queryTableName($_cid = 0)
    {
        $cid = input('param.cid/f', (float) $_cid);

        // 查找栏目所属模型
        $result =
        model('common/category')
        ->view('category c', ['id', 'name'])
        ->view('model m', ['name' => 'model_tablename'], 'm.id=c.model_id')
        ->where([
            ['c.id', '=', $cid],
        ])
        ->find();

        if ($result) {
            $result = $result->toArray();
            return $result['model_tablename'];
        }

        return false;
    }
}
