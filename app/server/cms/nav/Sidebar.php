<?php
/**
 *
 * API接口层
 * 侧导航
 *
 * @package   NiPHP
 * @category  app\server\cms\nav
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server\cms\nav;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\model\Category as ModelCategory;

class Sidebar
{

    /**
     * 侧导航
     * @access public
     * @param
     * @return array
     */
    public function query(): array
    {
        if ($cid = Request::param('cid/f', null)) {
            $result =
            ModelCategory::view('category c', ['id', 'name', 'aliases', 'image', 'access_id'])
            ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
            ->where([
                ['c.is_show', '=', 1],
                ['c.id', '=', $cid],
                ['c.lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . $cid, null, 'NAV')
            ->find()
            ->toArray();


            $result['image'] = !empty($result['image']) ? Config::get('cdn_host') . $result['image'] : '';
            $result['url'] = url($result['action_name'] . '/' . $result['id']);
            $result['child'] = $this->child($result['id']);
            if (empty($result['child'])) {
                unset($result['child']);
            }

            unset($result['action_name']);

            return [
                'debug' => false,
                'msg'   => Lang::get('success'),
                'data'  => $result
            ];
        } else {
            return [
                'debug' => false,
                'msg'   => Lang::get('error')
            ];
        }
    }

    /**
     * 获得子导航
     * @access private
     * @param  int    $_pid     父ID
     * @param  int    $_type_id 类型
     * @return array
     */
    private function child(int $_pid)
    {
        $result =
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->where([
            ['c.is_show', '=', 1],
            ['c.pid', '=', $_pid],
            ['c.lang', '=', Lang::detect()]
        ])
        ->order('c.sort ASC, c.id DESC')
        ->cache(__METHOD__ . $_pid, null, 'NAV')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $value['url'] = url($value['action_name'] . '/' . $value['id']);
            $value['child'] = $this->child($value['id'], 2);

            unset($value['action_name']);
            $result[$key] = $value;
        }

        return $result;
    }
}
