<?php
/**
 *
 * 列表 - 业务层
 *
 * @package   NiPHP
 * @category  application\book\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\logic;

class Listing
{

    /**
     * 章节列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/BookArticle')
        ->field(['id', 'title', 'book_id', 'update_time'])
        ->where([
            ['book_id', '=', input('param.bid/f')],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
        ])
        ->order('id ASC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);
            $result[$key]->url = url('article/' . $value->book_id . '/' . $value->id);
        }

        $list = $result->toArray();

        $data = [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
        ];

        return $data;
    }
}
