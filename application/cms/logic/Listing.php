<?php
/**
 *
 * 列表 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Listing
{

    /**
     * 文章列表
     * @access public
     * @param  integer $_cid 栏目ID
     * @return array
     */
    public function query($_cid = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');
        $table_name = $this->queryTableName($_cid);
        if (!$table_name) {
            return false;
        } elseif (in_array($table_name, ['article', 'download'])) {
            return $this->articleAndDownload($_cid, $table_name);
        } elseif (in_array($table_name, ['feedback', 'message'])) {
            return $this->feedbackAndMessage($_cid, $table_name);
        } elseif (in_array($table_name, ['picture', 'product'])) {
            return $this->pictureAndProduct($_cid, $table_name);
        } elseif ($table_name === 'link') {
            return $this->link($_cid);
        }
    }

    /**
     * 反馈和留言模型列表
     * feedback message
     * @access private
     * @param  integer $_cid 栏目ID
     * @return array
     */
    private function feedbackAndMessage($_cid, $_table_name)
    {
        $fields = [
            'id',
            'category_id',
            'title',
            'is_pass',
            'update_time',
            'create_time'
        ];

        $append = [
            'pass_name',
        ];

        $result =
        model('common/' . $_table_name)
        ->view($_table_name . ' a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', [], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.category_id', '=', $_cid]
        ])
        ->order('t.name DESC, a.id DESC')
        ->append($append)
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);

            $result[$key]->url = url($_table_name . '/' . $value->category_id . '/' . $value->id);

            $result[$key]->cat_url = url('list/' . $value->category_id);

            // 查询自定义字段
            $fields =
            model('common/' . $_table_name . 'Data')
            ->view($_table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'FIELDS' . $value->id : false)
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result[$key][$val['fields_name']] = $val['data'];
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
     * 图片和产品模型列表
     * picture product
     * @access private
     * @param  integer $_cid 栏目ID
     * @return array
     */
    private function pictureAndProduct($_cid, $_table_name)
    {
        $fields = [
            'id',
            'category_id',
            'title',
            'sort',
            'is_pass',
            'is_link',
            'is_com',
            'is_hot',
            'is_top',
            'is_link',
            'url',
            'update_time',
            'create_time'
        ];

        $append = [
            'pass_name',
            'com_name',
            'hot_name',
            'top_name',
            'link_name',
        ];

        $result =
        model('common/' . $_table_name)
        ->view($_table_name . ' a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', [], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()],
            ['a.category_id', '=', $_cid]
        ])
        ->order('a.is_top, a.is_hot, a.is_com, t.name DESC, a.sort DESC, a.id DESC')
        ->append($append)
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);

            if ($value->is_link) {
                $result[$key]->url = url('go/' . $value->category_id . '/' . $value->id);
            } else {
                $result[$key]->url = url($_table_name . '/' . $value->category_id . '/' . $value->id);
            }

            $result[$key]->cat_url = url('list/' . $value->category_id);

            // 查询自定义字段
            $fields =
            model('common/' . $table_name . 'Data')
            ->view($table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'FIELDS' . $value->id : false)
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result[$key][$val['fields_name']] = $val['data'];
            }

            // 查询相册
            $result[$key]['albums'] =
            model('common/' . $table_name . 'Album')
            ->field(true)
            ->where([
                ['main_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'ALBUMS' . $value->id : false)
            ->select()
            ->toArray();

            // 查询标签
            $result[$key]['tags'] =
            model('common/tagsArticle')
            ->view('tags_article a', ['tags_id'])
            ->view('tags t', ['name'], 't.id=a.tags_id')
            ->where([
                ['a.category_id', '=', $value->category_id],
                ['a.article_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'TAGS' . $value->category_id . $value->id : false)
            ->select()
            ->toArray();
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
     * 文章和下载模型列表
     * article download
     * @access private
     * @param  integer $_cid 栏目ID
     * @return array
     */
    private function articleAndDownload($_cid, $_table_name)
    {
        $fields = [
            'id',
            'category_id',
            'title',
            'sort',
            'is_pass',
            'is_link',
            'is_com',
            'is_hot',
            'is_top',
            'is_link',
            'url',
            'update_time',
            'create_time'
        ];

        $append = [
            'pass_name',
            'com_name',
            'hot_name',
            'top_name',
            'link_name',
        ];

        $result =
        model('common/' . $_table_name)
        ->view($_table_name . ' a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', [], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()],
            ['a.category_id', '=', $_cid]
        ])
        ->order('a.is_top, a.is_hot, a.is_com, t.name DESC, a.sort DESC, a.id DESC')
        ->append($append)
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);

            if ($value->is_link) {
                $result[$key]->url = url('go/' . $value->category_id . '/' . $value->id);
            } else {
                $result[$key]->url = url($_table_name . '/' . $value->category_id . '/' . $value->id);
            }

            $result[$key]->cat_url = url('list/' . $value->category_id);

            // 查询自定义字段
            $fields =
            model('common/' . $_table_name . 'Data')
            ->view($_table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? 'LISITING QDFI' . $value->id : false)
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result[$key][$val['fields_name']] = $val['data'];
            }

            // 查询标签
            $result[$key]['tags'] =
            model('common/tagsArticle')
            ->view('tags_article a', ['tags_id'])
            ->view('tags t', ['name'], 't.id=a.tags_id')
            ->where([
                ['a.category_id', '=', $value->category_id],
                ['a.article_id', '=', $value->id],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . $value->category_id . $value->id : false)
            ->select()
            ->toArray();
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
     * 友情链接模型列表
     * @access private
     * @param  integer $_cid 栏目ID
     * @return array
     */
    private function link($_cid)
    {
        $fields = ['id', 'category_id', 'title', 'sort', 'is_pass', 'url', 'update_time', 'create_time'];

        $result =
        model('common/link')
        ->view('link a', $fields)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', [], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=a.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.category_id', '=', $_cid]
        ])
        ->order('t.name DESC, a.sort DESC, a.id DESC')
        ->append(['pass_name'])
        ->cache(!APP_DEBUG ? __METHOD__ . $_cid : false)
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);

            $result[$key]->url = url('go/' . $value->category_id . '/' . $value->id);
        }

        $result = $result->toArray();

        return [
            'list'         => $result,
            'total'        => count($result),
            'per_page'     => 1,
            'current_page' => 1,
            'last_page'    => 1,
            'page'         => '',
        ];
    }

    /**
     * 获取对应的模型表名
     * @access public
     * @param  integer $_cid 栏目ID
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
        ->cache(!APP_DEBUG ? __METHOD__ . $cid : false)
        ->find();

        if ($result) {
            $result = $result->toArray();
            return $result['model_tablename'];
        } else {
            abort(404);
        }

        return false;
    }
}
