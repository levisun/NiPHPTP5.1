<?php
/**
 *
 * API接口层
 * 最新发布文章列表
 *
 * @package   NiPHP
 * @category  app\api\cms\v1_0\article
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\api\cms\v1_0\article;

use think\facade\Lang;

class News
{

    public function query(): array
    {
        return [
            'debug' => false,
            'msg' => Lang::get('success'),
            'data' => [
                'url' => url()
            ]
        ];
    }
}
