<?php
/**
 *
 * 管理书籍 - 书库 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\book
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\book;

use app\admin\logic\Upload;

class Book extends Upload
{

    /**
     * 查询
     * 书籍列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/book')
        ->view('book b', ['id', 'name', 'author_id', 'is_show', 'is_pass', 'is_com', 'is_top', 'is_hot', 'sort', 'hits', 'status'])
        ->view('book_type bt', ['name' => 'type_name'], 'bt.id=b.type_id')
        ->view('book_author ba', ['username' => 'author'], 'ba.id=b.author_id', 'LEFT')
        ->order('b.sort DESC, b.id DESC')
        ->append([
            'show',
            'pass',
            'status'
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'manage' => url('book/book', ['operate' => 'manage', 'id' => $value->id]),
                'editor' => url('book/book', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('book/book', ['operate' => 'remove', 'id' => $value->id]),
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
