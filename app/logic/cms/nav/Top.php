<?php
/**
 *
 * API接口层
 * 顶导航
 *
 * @package   NICMS
 * @category  app\logic\cms\nav
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\cms\nav;

use think\facade\Lang;
use app\library\Base64;
use app\model\Category as ModelCategory;

class Top
{

    /**
     * 顶导航
     * @access public
     * @param
     * @return array
     */
    public function query(): array
    {
        $result =
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image', 'is_channel', 'access_id'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->view('level level', ['name' => 'level_name'], 'level.id=c.access_id', 'LEFT')
        ->where([
            ['c.is_show', '=', 1],
            ['c.type_id', '=', 1],
            ['c.pid', '=', 0],
            ['c.lang', '=', Lang::detect()]
        ])
        ->order('c.sort_order ASC, c.id DESC')
        ->cache(__METHOD__, null, 'NAV')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $value['image'] = imgUrl($value['image']);
            $value['flag'] = Base64::flag($value['id'], 7);
            $value['child'] = $this->child($value['id'], 2);
            if (empty($value['child'])) {
                unset($value['child']);
            }

            $value['url'] = url('list/' . $value['action_name'] . '/' . $value['id']);
            if ($value['access_id']) {
                $value['url'] = url('channel/' . $value['action_name'] . '/' . $value['id']);
            }
            unset($value['action_name']);

            $result[$key] = $value;
        }

        return [
            'debug' => false,
            'msg'   => Lang::get('success'),
            'data'  => $result
        ];
    }

    /**
     * 获得子导航
     * @access private
     * @param  int    $_pid     父ID
     * @param  int    $_type_id 类型
     * @return array
     */
    private function child(int $_pid, int $_type_id)
    {
        $result =
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image', 'is_channel', 'access_id'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->view('level level', ['name' => 'level_name'], 'level.id=c.access_id', 'LEFT')
        ->where([
            ['c.is_show', '=', 1],
            ['c.type_id', '=', $_type_id],
            ['c.pid', '=', $_pid],
            ['c.lang', '=', Lang::detect()]
        ])
        ->order('c.sort_order ASC, c.id DESC')
        ->cache(__METHOD__ . $_pid . $_type_id, null, 'NAV')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $value['image'] = imgUrl($value['image']);
            $value['flag'] = Base64::flag($value['id'], 7);

            $value['url'] = url('list/' . $value['action_name'] . '/' . $value['id']);
            if ($value['access_id']) {
                $value['url'] = url('channel/' . $value['action_name'] . '/' . $value['id']);
            }
            unset($value['action_name']);

            $value['child'] = $this->child($value['id'], 2);

            $result[$key] = $value;
        }

        return $result;
    }
}
