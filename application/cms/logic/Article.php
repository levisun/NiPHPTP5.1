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
     * 文章详情
     * @access public
     * @param  integer $_cid 栏目ID
     * @param  integer $_id  文章ID
     * @return array
     */
    public function query($_cid = 0, $_id = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');
        $_id  = $_id  ? (float) $_id  : input('param.id/f');

        $table_name = $this->queryTableName($_cid);
        if (!$table_name || $table_name === 'link') {
            return false;
        }

        $result =
        model('common/' . $table_name)
        ->view($table_name . ' a', true)
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', [], 'm.id=c.model_id')
        ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')
        ->where([
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()],
            ['a.category_id', '=', $_cid],
            ['a.id', '=', $_id]
        ])
        ->cache(!APP_DEBUG ? 'ARTICLE FCI' . $_cid . $_id : false)
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);
            $result->title = htmlspecialchars_decode($result->title);
            $result->content = htmlspecialchars_decode($result->content);



            // 查询自定义字段
            $fields =
            model('common/' . $table_name . 'Data')
            ->view($table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $result->id],
            ])
            ->cache(!APP_DEBUG ? 'ARTICLE FDM' . $result->id : false)
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result->$val['fields_name'] = $val['data'];
            }



            // 查询相册
            if (in_array($table_name, ['picture', 'product'])) {
                $result->albums =
                model('common/' . $table_name . 'Album')
                ->field(true)
                ->where([
                    ['main_id', '=', $result->id],
                ])
                ->cache(!APP_DEBUG ? 'ARTICLE FAM' . $result->id : false)
                ->select()
                ->toArray();
            }



            // 查询标签
            $result->tags =
            model('common/tagsArticle')
            ->view('tags_article a', ['tags_id'])
            ->view('tags t', ['name'], 't.id=a.tags_id')
            ->where([
                ['a.category_id', '=', $result->category_id],
                ['a.article_id', '=', $result->id],
            ])
            ->cache(!APP_DEBUG ? 'ARTICLE FTCA' . $result['category_id'] . $result->id : false)
            ->select()
            ->toArray();

            // 上一篇
            if (in_array($table_name, ['article', 'download', 'picture', 'product'])) {
                $result->prev = $this->previous($_id, $_cid, $table_name);
                $result->next = $this->next($_id, $_cid, $table_name);
            }

            // 更新浏览数
            model('common/' . $table_name)
            ->where([
                ['is_pass', '=', 1],
                ['show_time', '<=', time()],
                ['category_id', '=', $_cid],
                ['id', '=', $_id]
            ])
            ->setInc('hits', 1);

            if (!$this->checkAccess($result->access_id)) {
                $result = 'not access';
            }

            return $result->toArray();;
        } else {
            return false;
        }
    }

    /**
     * 下一篇
     * @access private
     * @param  integer $_cid 栏目ID
     * @param  integer $_id  文章ID
     * @param  string  $_table_name
     * @return mixed
     */
    private function next($_id, $_cid, $_table_name)
    {
        $next_id =
        model('common/' . $_table_name)
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '>', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort DESC, id DESC')
        ->min('id');

        $result =
        model('common/' . $_table_name)
        ->field(['id', 'category_id', 'title', 'is_link', 'url'])
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '=', $next_id]
        ])
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);

            if (!empty($result->is_link) && $result->is_link) {
                $result->url  = url('go/' . $result->category_id . '/' . $result->id);
                $result->url .= '?go=' . urlencode($result->url);
            } else {
                $result->url = url($_table_name . '/' . $result->category_id . '/' . $result->id);
            }

            $result->url = str_replace('/index/', '/', $result->url);

            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 上一篇
     * @access private
     * @param  integer $_cid 栏目ID
     * @param  integer $_id  文章ID
     * @param  string  $_table_name
     * @return mixed
     */
    private function previous($_id, $_cid, $_table_name)
    {
        $previous_id =
        model('common/' . $_table_name)
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '<', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort DESC, id DESC')
        ->max('id');

        $result =
        model('common/' . $_table_name)
        ->field(['id', 'category_id', 'title', 'is_link', 'url'])
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '=', $previous_id]
        ])
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);

            if (!empty($result->is_link) && $result->is_link) {
                $result->url  = url('go/' . $result->category_id . '/' . $result->id);
                $result->url .= '?go=' . urlencode($result->url);
            } else {
                $result->url = url($_table_name . '/' . $result->category_id . '/' . $result->id);
            }

            $result->url = str_replace('/index/', '/', $result->url);

            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 更新点击数
     * @access public
     * @param  integer $_cid 栏目ID
     * @param  integer $_id  文章ID
     * @return array
     */
    public function hits($_cid = 0, $_id = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');
        $_id  = $_id  ? (float) $_id  : input('param.id/f');

        if (!$table_name = $this->queryTableName($_cid)) {
            return false;
        }

        // 更新浏览数
        model('common/' . $table_name)
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '=', $_id]
        ])
        ->setInc('hits', 1);

        return
        model('common/' . $table_name)
        ->field(['hits', 'comment_count'])
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['category_id', '=', $_cid],
            ['id', '=', $_id]
        ])
        ->cache(!APP_DEBUG ? 'ARTICLE HCI' . $_cid . $_id : false, 60)
        ->find();
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
        ->cache(!APP_DEBUG ? 'TABLE QTN' . $cid : false)
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
