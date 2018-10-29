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

use app\admin\logic\Upload;

class Content extends Upload
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
                $url['child'] = url('content/content', ['operate' => 'child', 'pid' => $value['id']]);
            }

            if ($value['model_id'] == 4) {
                $url['manage'] = url('content/content', ['operate' => 'editor', 'model' => 'page', 'cid' => $value['id']]);
            } else {
                $url['manage'] = url('content/content', ['operate' => 'manage', 'cid' => $value['id']]);
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
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('content/content', ['operate' => 'editor', 'model' => $table_name, 'cid' => $value->category_id, 'id' => $value->id]),
                'remove' => url('content/content', ['operate' => 'remove', 'model' => $table_name, 'cid' => $value->category_id, 'id' => $value->id]),
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

            // 查询相册
            if (in_array($table_name, ['picture', 'product'])) {
                $result['albums'] =
                model('common/' . $table_name . 'Album')
                ->field(true)
                ->where([
                    ['main_id', '=', $result['id']],
                ])
                ->select()
                ->toArray();
            }

            // 查询标签
            $result['tags'] =
            model('common/tagsArticle')
            ->view('tags_article a', ['tags_id'])
            ->view('tags t', ['name'], 't.id=a.tags_id')
            ->where([
                ['a.category_id', '=', $result['category_id']],
                ['a.article_id', '=', $result['id']],
            ])
            ->select()
            ->toArray();
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
    {
        $receive_data = [
            'id'            => input('post.id/f'),
            'title'         => input('post.title'),
            'keywords'      => input('post.keywords'),
            'description'   => input('post.description'),
            'content'       => input('post.content', '', config('content_filter')),
            'thumb'         => input('post.thumb'),
            'category_id'   => input('post.category_id/f'),
            'type_id'       => input('post.type_id/f'),
            'is_pass'       => input('post.is_pass/f'),
            'is_com'        => input('post.is_com/f'),
            'is_top'        => input('post.is_top/f'),
            'is_hot'        => input('post.is_hot/f'),
            'sort'          => input('post.sort/f'),
            'username'      => input('post.username'),
            'origin'        => input('post.origin'),
            'user_id'       => input('post.user_id/f'),
            'url'           => input('post.url'),
            'is_link'       => input('post.is_link/f'),
            // 'show_time'     => input('post.show_time'),
            // 'delete_time'   => input('post.title'),
            'access_id'     => input('post.access_id/f'),
            'lang'          => lang(':detect'),
            '__token__'     => input('post.__token__'),
        ];

        $table_name = $this->queryTableName();
        halt($table_name);
        $result = validate('admin/content/content.editor', input('post.'));
        if (true !== $result) {
            return $result;
        }

        print_r($_POST);
    }

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

    public function type()
    {
        return
        model('common/type')
        ->where([
            ['category_id', '=', input('post.cid/f')],
        ])
        ->select();
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
