<?php
/**
 *
 * 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\book\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\logic;

class Article
{

    /**
     * 文章详情
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/BookArticle')
        ->where([
            ['id', '=', input('param.id/f')],
            ['book_id', '=', input('param.bid/f')],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
        ])
        ->cache(!APP_DEBUG ? 'BOOKARTICLE QBI' . input('param.bid/f') . input('param.id/f') : false)
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);
            $result->title = htmlspecialchars_decode($result->title);
            $result->content = htmlspecialchars_decode($result->content);

            $result->prev = $this->previous(input('param.id/f'), input('param.bid/f'));
            $result->next = $this->next(input('param.id/f'), input('param.bid/f'));

            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 下一篇
     * @access private
     * @param  integer $_bid 书ID
     * @param  integer $_id  文章ID
     * @return mixed
     */
    private function next($_id, $_bid)
    {
        $next_id =
        model('common/BookArticle')
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['book_id', '=', $_bid],
            ['id', '>', $_id]
        ])
        ->order('id DESC')
        ->min('id');

        $result =
        model('common/BookArticle')
        ->field(['id', 'book_id', 'title'])
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['book_id', '=', $_bid],
            ['id', '=', $next_id]
        ])
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);
            $result->title = htmlspecialchars_decode($result->title);
            $result->url = url('article/' . $result->book_id . '/' . $result->id);
            $result->url = str_replace('/index/', '/', $result->url);

            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 上一篇
     * @access private
     * @param  integer $_bid 栏目ID
     * @param  integer $_id  文章ID
     * @return mixed
     */
    private function previous($_id, $_bid)
    {
        $prev_id =
        model('common/BookArticle')
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['book_id', '=', $_bid],
            ['id', '>', $_id]
        ])
        ->order('id DESC')
        ->max('id');

        $result =
        model('common/BookArticle')
        ->field(['id', 'book_id', 'title'])
        ->where([
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
            ['book_id', '=', $_bid],
            ['id', '=', $prev_id]
        ])
        ->find();

        if ($result) {
            $result->flag = encrypt($result->id);
            $result->title = htmlspecialchars_decode($result->title);
            $result->url = url('article/' . $result->book_id . '/' . $result->id);
            $result->url = str_replace('/index/', '/', $result->url);

            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 更新点击数
     * @access public
     * @param
     * @return array
     */
    public function hits()
    {
        // 更新浏览数
        model('common/BookArticle')
        ->where([
            ['id', '=', input('param.id/f')],
            ['book_id', '=', input('param.bid/f')],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
        ])
        ->setInc('hits', 1);

        return
        model('common/BookArticle')
        ->field(['hits'])
        ->where([
            ['id', '=', input('param.id/f')],
            ['book_id', '=', input('param.bid/f')],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()],
        ])
        ->cache(!APP_DEBUG ? 'BOOKARTICLE QBIH' . input('param.bid/f') . input('param.id/f') : false)
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
}
