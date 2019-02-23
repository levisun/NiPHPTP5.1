<?php
/**
 *
 * API接口层
 * 网站导航
 *
 * @package   NiPHP
 * @category  app\api\cms\v1_0\nav
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\api\cms\v1_0\nav;

use think\facade\Lang;
use app\model\Category as ModelCategory;

class Top
{

    /**
     * 主导航
     * @access private
     * @param
     * @return array
     */
    public function query(): array
    {
        $result =
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->where([
            ['c.is_show', '=', 1],
            ['c.type_id', '=', 1],
            ['c.pid', '=', 0],
            ['c.lang', '=', Lang::detect()]
        ])
        ->order('c.sort ASC, c.id DESC')
        ->cache(__METHOD__, null, 'NAV')
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $value['url'] = url($value['action_name'] . '/' . $value['id']);
            $value['child'] = $this->child($value['id'], 1);
            if (empty($value['child'])) {
                unset($value['child']);
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
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->where([
            ['c.is_show', '=', 1],
            ['c.type_id', '=', $_type_id],
            ['c.pid', '=', $_pid],
            ['c.lang', '=', Lang::detect()]
        ])
        ->order('c.sort ASC, c.id DESC')
        ->cache(__METHOD__ . $_pid . $_type_id, null, 'NAV')
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
