<?php
/**
 *
 * API接口层
 * 文章列表
 *
 * @package   NICMS
 * @category  app\logic\cms\link
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\cms\link;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\model\Link as ModelLink;
use app\library\Base64;

class Lists
{

    /**
     * 查询列表
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $map = [
            ['link.is_pass', '=', '1'],
            ['link.show_time', '<=', time()],
            ['link.lang', '=', Lang::detect()]
        ];

        if ($category_id = Request::param('cid/f', null)) {
            $map[] = ['link.category_id', '=', $category_id];
        } else {
            return [
                'debug' => false,
                'cache' => false,
                'msg'   => Lang::get('param error'),
                'data'  => Request::param('', [], 'trim')
            ];
        }

        $result =
        ModelLink::view('link link', ['id', 'category_id', 'title', 'url', 'logo'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=link.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id')
        ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=link.type_id', 'LEFT')
        ->where($map)
        ->order('link.sort_order DESC, link.id DESC')
        ->cache(__METHOD__ . $category_id, null, 'LISTS')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $value['logo'] = imgUrl($value['logo'], 100, 50);
        }

        return [
            'debug' => false,
            'msg'   => Lang::get('success'),
            'data'  => [
                'list'         => $result,
                'total'        => count($result),
                // 'per_page'     => $list['per_page'],
                // 'current_page' => $list['current_page'],
                // 'last_page'    => $list['last_page'],
                // 'page'         => $list['render'],
            ]
        ];
    }
}
