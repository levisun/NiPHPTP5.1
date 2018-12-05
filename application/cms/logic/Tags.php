<?php
/**
 *
 * 标签 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Tags
{

    /**
     * 查询标签
     * @access public
     * @param  int $data
     * @return array
     */
    public function query()
    {
        $result =
        model('common/tags')
        ->field('id,name,count')
        ->where([
            ['lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG ? __METHOD__ : false)
        ->select();

        $result = $result ? $result->toArray() : [];

        foreach ($result as $key => $value) {
            $result[$key]['url'] = url('/tags/' . $value['id']);
        }

        return $result;
    }

    public function article()
    {
        $result =
        model('common/tags')
        ->view('tags t', 'id')
        ->view('tags_article ta', ['category_id', 'article_id'], 'ta.tags_id=t.id')
        ->where([
            ['t.id', '=', input('param.id/f')],
            ['t.lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG ? __METHOD__ : false)
        ->select()
        ->toArray();

        $article_id = $category_id = [];
        foreach ($result as $key => $value) {
            $article_id[] = $value['article_id'];
            $category_id[] = $value['category_id'];
        }
        $category_id = array_unique($category_id);

        $result =
        model('common/article')
        ->view('article a', ['id', 'category_id'], 'a.id in(' . implode(',', $article_id) . ') and a.category_id in(' . implode(',', $category_id) . ')', 'left')
        ->view('picture p', ['id', 'category_id'], 'p.id in(' . implode(',', $article_id) . ') and p.category_id in(' . implode(',', $category_id) . ')', 'left')
        // ->view('type t', ['name' => 'type_name'], 't.id=c.type_id', 'LEFT')

        // ->order('t.name DESC, a.id DESC')
        // ->append($append)
        ->paginate();

        print_r(model('common/article')->getlastsql());
        halt(1);
    }
}
