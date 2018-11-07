<?php
/**
 *
 * 书籍 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\book\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\logic;

class Book
{
    /**
     * 书籍列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/book')
        ->field(['id', 'name', 'hits', 'update_time'])
        ->where([
            ['is_show', '=', 1],
            ['is_pass', '=', 1],
        ])
        ->order('id ASC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->flag   = encrypt($value->id);
            $result[$key]->title = htmlspecialchars_decode($value->title);
            $result[$key]->url = url('book/' . $value->id);
            $result[$key]->url = str_replace('/index/', '/', $result[$key]->url);
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
