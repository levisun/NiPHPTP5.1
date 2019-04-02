<?php
/**
 *
 * API接口层
 * 面包屑
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
use think\facade\Request;
use app\library\Base64;
use app\model\Category as ModelCategory;

class Breadcrumb
{
    private $bread = [];

    /**
     * 面包屑
     * @access public
     * @param
     * @return array
     */
    public function query(): array
    {
        $cid = Request::param('cid/f', null);
        $this->parentCate($cid);

        return [
            'debug' => false,
            'msg'   => Lang::get('success'),
            'data'  => $this->bread
        ];
    }

    /**
     * 获得父导航
     * @access private
     * @param  int     $_pid 父ID
     * @param
     * @return array
     */
    private function parentCate($_pid)
    {
        $result =
        ModelCategory::view('category c', ['id', 'name', 'pid', 'aliases', 'image', 'is_channel', 'access_id'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->view('level level', ['name' => 'level_name'], 'level.id=c.access_id', 'LEFT')
        ->where([
            ['c.is_show', '=', 1],
            ['c.id', '=', $_pid],
            ['c.lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . $_pid, null, 'NAV')
        ->find()
        ->toArray();

        if ($result) {
            $result['image'] = imgUrl($result['image']);
            $result['flag'] = Base64::flag($result['id'], 7);

            $result['url'] = url('list/' . $result['action_name'] . '/' . $result['id']);
            if ($result['access_id']) {
                $result['url'] = url('channel/' . $result['action_name'] . '/' . $result['id']);
            }
            unset($result['action_name']);

            $this->bread[] = $result;
            if ($result['pid']) {
                $this->parentCate($result['pid']);
            }
        }
    }
}
