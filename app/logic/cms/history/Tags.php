<?php
/**
 *
 * API接口层
 * 历史记录
 *
 * @package   NICMS
 * @category  app\logic\cms\history
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\cms\history;

// use think\facade\Cache;
// use think\facade\Config;
// use think\facade\Lang;
// use think\facade\Request;
use app\model\TagsArticle as ModelTagsArticle
// use app\library\Base64;

class Tags
{

    /**
     * 记录浏览的标签信息
     * @return [type] [description]
     */
    public function record()
    {
        if ($id = Request::param('id/f', null)) {
            $map[
                ['article_id', '=', $id]
            ];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        $result =
        ModelTagsArticle::view('tags_article article')
        ->view('tags tags', ['name' => 'tags_name'], 'tags.id=article.tags_id')
        ->where($map)
        ->cache(__METHOD__ . $id, null, 'HISTORY')
        ->select()
        ->toArray();
    }

    /**
     * 清除过期的记录
     * @return [type] [description]
     */
    public function remove()
    {
        # code...
    }
}
