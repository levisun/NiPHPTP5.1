<?php
/**
 *
 * API接口层
 * 文章基础类
 *
 * @package   NiPHP
 * @category  app\logic\cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\cms;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\library\Base64;
use app\model\Article as ModelArticle;
use app\model\ArticleContent as ModelArticleContent;
use app\model\ArticleData as ModelArticleData;
use app\model\TagsArticle as ModelTagsArticle;

class ArticleBase
{

    /**
     * 查询列表
     * @access protected
     * @param
     * @return array
     */
    protected function lists()
    {
        $map = [
            ['article.is_pass', '=', '1'],
            ['article.show_time', '<=', time()],
            ['article.lang', '=', Lang::detect()]
        ];

        if ($category_id = Request::param('cid/f', null)) {
            $map[] = ['article.category_id', '=', $category_id];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        if ($com = Request::param('com/f', 0)) {
            $map[] = ['article.is_com', '=', '1'];
        } elseif ($top = Request::param('top/f', 0)) {
            $map[] = ['article.is_top', '=', '1'];
        } elseif ($hot = Request::param('hot/f', 0)) {
            $map[] = ['article.is_hot', '=', '1'];
        }

        if ($type_id = Request::param('tid/f', null)) {
            $map[] = ['article.type_id', '=', $type_id];
        }

        $query_limit = (int) Request::param('limit/f', 20);
        $query_page = (int) Request::param('page/f', 1);

        $cache_key = md5(count($map) . $category_id . $com . $top . $hot . $type_id . $query_limit . $query_page);
        if (!Cache::has($cache_key)) {
            $result =
            ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
            ->view('article_content article_content', ['thumb'], 'article_content.article_id=article.id', 'LEFT')
            ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
            ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id')
            ->view('level level', ['name' => 'level_name'], 'level.id=article.access_id', 'LEFT')
            ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=article.type_id', 'LEFT')
            ->where($map)
            ->order('article.is_top DESC, article.is_hot DESC , article.is_com DESC, article.sort_order DESC, article.id DESC')
            ->paginate($query_limit);
            $list = $result->toArray();
            $list['render'] = $result->render();

            Cache::tag('catalog')->set($cache_key, $list);
        } else {
            $list = Cache::get($cache_key);
        }

        $date_format = Request::param('date_format', 'Y-m-d');

        foreach ($list['data'] as $key => $value) {
            $value['flag'] = Base64::flag($value['category_id'] . $value['id'], 7);
            $value['cat_url'] = url('list/' . $value['action_name'] . '/' . $value['category_id']);
            $value['url'] = url('details/' . $value['action_name'] . '/' . $value['category_id'] . '/' . $value['id']);
            $value['thumb'] = imgUrl($value['thumb'], 150, 150);
            $value['update_time'] = date($date_format, $value['update_time']);


            // 附加字段数据
            // $fields =
            // ModelArticleData::view('article_data data', ['data'])
            // ->view('fields fields', ['name' => 'fields_name'], 'fields.id=data.fields_id')
            // ->where([
            //     ['data.main_id', '=', $value['id']],
            // ])
            // ->cache('modelarticledata' . $value['id'], null, 'LISTS')
            // ->select()
            // ->toArray();
            // foreach ($fields as $val) {
            //    $value[$val['fields_name']] = $val['data'];
            // }


            // 标签
            $value['tags'] =
            ModelTagsArticle::view('tags_article article', ['tags_id'])
            ->view('tags tags', ['name'], 'tags.id=article.tags_id')
            ->where([
                ['article.article_id', '=', $value['id']],
            ])
            ->cache(__METHOD__ . 'tags' . $value['id'], null, 'LISTS')
            ->select()
            ->toArray();

            $list['data'][$key] = $value;
        }

        return $list;
    }

    /**
     * 查询内容
     * @access protected
     * @param
     * @return array
     */
    protected function details(): array
    {
        $map = [
            ['article.is_pass', '=', '1'],
            ['article.show_time', '<=', time()],
            ['article.lang', '=', Lang::detect()]
        ];

        if ($id = Request::param('id/f', null)) {
            $map[] = ['article.id', '=', $id];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
        ->view('article_content article_content', ['thumb', 'content'], 'article_content.article_id=article.id', 'LEFT')
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
        ->view('level level', ['name' => 'level_name'], 'level.id=article.access_id', 'LEFT')
        ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=article.type_id', 'LEFT')
        ->where($map)
        ->cache(__METHOD__ . $id, null, 'DETAILS')
        ->find()
        ->toArray();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url('details/' . $result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url'] = url('list/' . $result['action_name'] . '/' . $result['category_id']);
            $result['thumb'] = imgUrl($result['thumb'], 150, 150);
            $result['content'] = htmlspecialchars_decode($result['content']);

            $date_format = Request::param('date_format', 'Y-m-d');
            $result['update_time'] = date($date_format, $result['update_time']);


            // 附加字段数据
            // $fields =
            // ModelArticleData::view('article_data data', ['data'])
            // ->view('fields fields', ['name' => 'fields_name'], 'fields.id=data.fields_id')
            // ->where([
            //     ['data.main_id', '=', $value['id']],
            // ])
            // ->cache('modelarticledata' . $value['id'], null, 'LISTS')
            // ->select()
            // ->toArray();
            // foreach ($fields as $val) {
            //    $value[$val['fields_name']] = $val['data'];
            // }


            // 上一篇
            // 下一篇
            $result['next'] = $this->next($result['id']);
            $result['prev'] = $this->prev($result['id']);


            // 标签
            $result['tags'] =
            ModelTagsArticle::view('tags_article article', ['tags_id'])
            ->view('tags tags', ['name'], 'tags.id=article.tags_id')
            ->where([
                ['article.article_id', '=', $result['id']],
            ])
            ->cache(__METHOD__ . 'tags' . $result['id'], null, 'DETAILS')
            ->select()
            ->toArray();
        }

        return $result;
    }

    /**
     * 更新浏览量
     * @access protected
     * @param
     * @return array
     */
    protected function hits()
    {
        $map = [
            ['is_pass', '=', '1'],
            ['show_time', '<=', time()],
            ['lang', '=', Lang::detect()]
        ];

        if ($id = Request::param('id/f', null)) {
            $map[] = ['id', '=', $id];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        $time =
        ModelArticleContent::where([
            ['article_id', '=', $id]
        ])
        ->cache(__METHOD__ . 'articlecontent' . $id, null, 'DETAILS')
        ->value('content');
        $time = htmlspecialchars_decode($time);
        $time = strip_tags($time);
        $time = (int) ceil(strlen($time) / 40);

        // 更新浏览数
        ModelArticle::where($map)
        ->inc('hits', 1, $time)
        ->update();

        $result =
        ModelArticle::where($map)
        ->cache(__METHOD__ . $id, 15, 'DETAILS')
        ->value('hits');

        return $result;
    }

    /**
     * 下一篇
     * @access protected
     * @param  int      $_id
     * @return array
     */
    protected function next(int $_id)
    {
        $next_id =
        ModelArticle::where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['id', '>', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort_order DESC, id DESC')
        ->cache(__METHOD__ . 'min' . $_id, null, 'DETAILS')
        ->min('id');

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
        ->where([
            ['article.is_pass', '=', 1],
            ['article.show_time', '<=', time()],
            ['article.id', '=', $next_id]
        ])
        ->cache(__METHOD__ . 'eq' . $_id, null, 'DETAILS')
        ->find();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url('details/' . $result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url'] = url('list/' . $result['action_name'] . '/' . $result['category_id']);
        }

        return $result;
    }

    /**
     * 上一篇
     * @access protected
     * @param  int      $_id
     * @return array
     */
    protected function prev(int $_id)
    {
        $prev_id =
        ModelArticle::where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['id', '<', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort_order DESC, id DESC')
        ->cache(__METHOD__ . 'max' . $_id, null, 'DETAILS')
        ->max('id');

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
        ->where([
            ['article.is_pass', '=', 1],
            ['article.show_time', '<=', time()],
            ['article.id', '=', $prev_id]
        ])
        ->cache(__METHOD__ . 'eq' . $_id, null, 'DETAILS')
        ->find();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url('details/' . $result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url'] = url('list/' . $result['action_name'] . '/' . $result['category_id']);
        }

        return $result;
    }
}
