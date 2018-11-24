<?php
/**
 *
 * 管理书籍 - 书库 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\book
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\book;

class Article
{

    /**
     * 查询
     * 章节列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/bookArticle')
        ->field(['id', 'title', 'is_pass', 'update_time', 'create_time'])
        ->order('sort DESC, id DESC')
        ->where([
            ['book_id', '=', input('param.bid')]
        ])
        ->append(['pass'])
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('book/book', ['operate' => 'article_editor', 'bid' => $value->book_id, 'id' => $value->id]),
                'remove' => url('book/book', ['operate' => 'article_remove', 'bid' => $value->book_id, 'id' => $value->id]),
            ];
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
}
