<?php
/**
 *
 * API接口层
 * 文章内容
 *
 * @package   NiPHP
 * @category  app\api\cms\v1m0\picture
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\api\cms\v1m0\picture;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\library\Base64;
use app\model\Article as ModelArticle;
use app\model\ArticleData as ModelArticleData;
use app\model\TagsArticle as ModelTagsArticle;

class Details
{

    public function query(): array
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
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url', 'keywords', 'description', 'access_id', 'update_time'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=2')
        ->where($map)
        ->cache(__METHOD__ . $id, null, 'DETAILS')
        ->find()
        ->toArray();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url($result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url']  = url($result['action_name'] . '/' . $result['category_id']);
            $result['thumb'] = empty($result['thumb']) ? Config::get('cdn_host') . $result['thumb'] : '';


            // 上一篇
            // 下一篇
            $result['next_article'] = $this->next($result['id']);
            $result['prev_article'] = $this->prev($result['id']);


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

            return [
                'debug' => false,
                'msg'   => Lang::get('success'),
                'data'  => $result
            ];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('article not'),
                'data'  => Request::param('', [], 'trim')
            ];
        }
    }

    /**
     * 更新浏览量
     * @access public
     * @param
     * @return array
     */
    public function hits(): array
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
        ->setInc('hits', 1);

        $result =
        ModelArticle::where($map)
        ->cache(__METHOD__ . $id, 30, 'DETAILS')
        ->value('hits');

        return [
            'debug' => false,
            'expire' => 30,
            'msg'   => Lang::get('success'),
            'data'  => $result
        ];
    }

    /**
     * 下一篇
     * @access public
     * @param  int      $_id
     * @return array
     */
    public function next(int $_id): array
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
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=2')
        ->where([
            ['article.is_pass', '=', 1],
            ['article.show_time', '<=', time()],
            ['article.id', '=', $next_id]
        ])
        ->cache(__METHOD__ . 'eq' . $_id, null, 'DETAILS')
        ->find()
        ->toArray();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url($result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url']  = url($result['action_name'] . '/' . $result['category_id']);
        }

        return $result;
    }

    /**
     * 上一篇
     * @access public
     * @param  int      $_id
     * @return array
     */
    public function prev(int $_id): array
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
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=2')
        ->where([
            ['article.is_pass', '=', 1],
            ['article.show_time', '<=', time()],
            ['article.id', '=', $prev_id]
        ])
        ->cache(__METHOD__ . 'eq' . $_id, null, 'DETAILS')
        ->find()
        ->toArray();

        if ($result) {
            $result['flag'] = Base64::flag($result['category_id'] . $result['id'], 7);
            $result['url'] = url($result['action_name'] . '/' . $result['category_id'] . '/' . $result['id']);
            $result['cat_url']  = url($result['action_name'] . '/' . $result['category_id']);
        }

        return $result;
    }
}
