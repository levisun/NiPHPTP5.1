<?php
/**
 *
 * 列表 - 业务层
 *
 * @package   NiPHPCMS
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
     * 文章列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        if ($data = cache('BOOKARTICLE QBI' . input('param.bid/f'))) {
            return $data;
        }

        $result =
        model('common/BookArticle')
        ->where([
            ['book_id', '=', input('param.bid/f')],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);

            $result[$key]->url = url('article/' . $value->book_id . '/' . $value->id);
            $result[$key]->url = str_replace('/index/', '/', $result[$key]->url);
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

        if (!APP_DEBUG) {
            cache('BOOKARTICLE QBI' . input('param.bid/f'), $data);
        }

        return $data;
    }
}
