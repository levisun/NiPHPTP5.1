<?php
/**
 *
 * API接口层
 * 反馈内容
 *
 * @package   NiPHP
 * @category  app\api\cms\v1m0\feedback
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\api\cms\v1m0\feedback;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\model\Article as ModelArticle;
use app\server\Base64;

class Details
{

    public function query()
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
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        $result =
        ModelArticle::view('article article', ['id', 'category_id', 'title', 'thumb', 'url', 'keywords', 'description', 'access_id', 'update_time'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
        ->where($map)
        ->find();
        $result = $result->toArray();

        return [
            'debug' => false,
            'msg'   => Lang::get('success'),
            'data'  => $result
        ];
    }
}
