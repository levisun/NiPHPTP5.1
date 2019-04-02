<?php
/**
 *
 * API接口层
 * 文章内容
 *
 * @package   NICMS
 * @category  app\logic\cms\article
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\cms\article;

use think\facade\Cache;
use think\facade\Lang;
use think\facade\Request;
use app\logic\cms\ArticleBase;

class Details extends ArticleBase
{

    /**
     * 查询内容
     * @access public
     * @param
     * @return array
     */
    public function query(): array
    {
        if ($result = $this->details()) {

            $result['content'] = preg_replace('/(style=["|\'])(.*?)(["|\'])/si', '', $result['content']);


            // $result['content']



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
        $result = parent::hits();

        return [
            'debug' => false,
            'expire' => 30,
            'msg'   => Lang::get('success'),
            'data'  => $result
        ];
    }
}
