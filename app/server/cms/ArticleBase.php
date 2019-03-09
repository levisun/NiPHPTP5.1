<?php
/**
 *
 * API接口层
 * 文章基础类
 *
 * @package   NiPHP
 * @category  app\server\cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server\cms;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\library\Base64;
use app\model\Article as ModelArticle;
use app\model\ArticleData as ModelArticleData;
use app\model\TagsArticle as ModelTagsArticle;

class ArticleBase
{

    /**
     * 查询列表
     * @access public
     * @param
     * @return array
     */
    public function catalog()
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

        $cache_key = md5(count($map) . $category_id . $com . $top . $hot . $type_id);
        if (!Cache::has($cache_key)) {
            $result =
            ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url', 'keywords', 'description', 'access_id', 'update_time'])
            ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
            ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
            ->view('level level', ['name' => 'level_name'], 'level.id=article.access_id', 'LEFT')
            ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=article.type_id', 'LEFT')
            ->where($map)
            ->order('article.is_top DESC, article.is_hot DESC , article.is_com DESC, article.sort DESC, article.id DESC')
            ->paginate();
            $list = $result->toArray();
            $list['render'] = $result->render();

            Cache::tag('catalog')->set($cache_key, $list);
        } else {
            $list = Cache::get($cache_key);
        }

        foreach ($list['data'] as $key => $value) {
            $value['flag'] = Base64::flag($value['category_id'] . $value['id'], 7);
            $value['thumb'] = imgUrl($value['thumb']);
            $value['cat_url'] = url('list/' . $value['action_name'] . '/' . $value['category_id']);
            $value['url'] = url('details/' . $value['action_name'] . '/' . $value['category_id'] . '/' . $value['id']);


            // 附加字段数据
            $fields =
            ModelArticleData::view('article_data data', ['data'])
            ->view('fields fields', ['name' => 'fields_name'], 'fields.id=data.fields_id')
            ->where([
                ['data.main_id', '=', $value['id']],
            ])
            ->cache('modelarticledata' . $value['id'], null, 'CATALOG')
            ->select()
            ->toArray();
            foreach ($fields as $val) {
               $value[$val['fields_name']] = $val['data'];
            }


            // 标签
            $value['tags'] =
            ModelTagsArticle::view('tags_article article', ['tags_id'])
            ->view('tags tags', ['name'], 'tags.id=article.tags_id')
            ->where([
                ['article.article_id', '=', $value['id']],
            ])
            ->cache('modeltagsarticle' . $value['id'], null, 'CATALOG')
            ->select()
            ->toArray();

            $list['data'][$key] = $value;
        }

        return $list;
    }

    /**
     * 查询内容
     * @access public
     * @param
     * @return array
     */
    public function details(): array
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
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url' => 'go_url', 'keywords', 'description', 'access_id', 'update_time'])
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
            $result['thumb'] = imgUrl($result['thumb']);
            $result['url'] = url('details/' . $result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url'] = url('list/' . $result['action_name'] . '/' . $result['category_id']);


            // 上一篇
            // 下一篇
            $result['next'] = $this->next($result['id']);
            $result['prev'] = $this->prev($result['id']);


            // 附加字段数据
            $fields =
            ModelArticleData::view('article_data data', ['data'])
            ->view('fields fields', ['name' => 'fields_name'], 'fields.id=data.fields_id')
            ->where([
                ['data.main_id', '=', $result['id']],
            ])
            ->cache('modelarticledata' . $result['id'], null, 'DETAILS')
            ->select()
            ->toArray();
            foreach ($fields as $val) {
               $result[$val['fields_name']] = $val['data'];
            }


            // 标签
            $result['tags'] =
            ModelTagsArticle::view('tags_article article', ['tags_id'])
            ->view('tags tags', ['name'], 'tags.id=article.tags_id')
            ->where([
                ['article.article_id', '=', $result['id']],
            ])
            ->cache('modeltagsarticle' . $result['id'], null, 'DETAILS')
            ->select()
            ->toArray();
        }

        return $result;
    }

    /**
     * 更新浏览量
     * @access public
     * @param
     * @return array
     */
    public function upHits()
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

        // 更新浏览数
        ModelArticle::where($map)
        ->inc('hits', 1)
        ->update();

        $result =
        ModelArticle::where($map)
        ->cache(__METHOD__ . $id, 15, 'DETAILS')
        ->value('hits');

        return $result;
    }

    /**
     * 下一篇
     * @access public
     * @param  int      $_id
     * @return array
     */
    public function next(int $_id)
    {
        $next_id =
        ModelArticle::where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['id', '>', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort DESC, id DESC')
        ->cache(__METHOD__ . 'min' . $_id, null, 'DETAILS')
        ->min('id');

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url', 'keywords', 'description', 'access_id', 'update_time'])
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
     * @access public
     * @param  int      $_id
     * @return array
     */
    public function prev(int $_id)
    {
        $prev_id =
        ModelArticle::where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['id', '<', $_id]
        ])
        ->order('is_top, is_hot, is_com, sort DESC, id DESC')
        ->cache(__METHOD__ . 'max' . $_id, null, 'DETAILS')
        ->max('id');

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url', 'keywords', 'description', 'access_id', 'update_time'])
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
