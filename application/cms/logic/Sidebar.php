<?php
/**
 *
 * 侧导航 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Sidebar
{

    /**
     * 查询侧导航
     * @access public
     * @param  array $data
     * @return array
     */
    public function query()
    {
        $cat_id = input('param.cid/f', 0);

        $id = $this->queryParent($cat_id);

        $map = [
            ['id', '=', $id],
            ['is_show', '=', 1],
            ['pid', '=', 0],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->field('id,name,pid,aliases,image,url')
        ->where($map)
        ->order('sort ASC, id DESC')
        ->cache(!APP_DEBUG)
        ->select();

        $result = $result->toArray();

        $result = $this->queryChild($result);

        return !empty($result[0]) ? $result[0] : [];
    }

    /**
     * 查询子导航
     * @access protected
     * @param  array $data
     * @return array
     */
    protected function queryChild($_data)
    {
        $map = [
            ['lang', '=', lang(':detect')],
        ];

        $nav = [];

        foreach ($_data as $key => $value) {
            $nav[$key] = $value;
            $nav[$key]['url'] = url('/list/' . $value['id']);

            $map[] = ['pid', '=', $value['id']];

            $result =
            model('common/category')
            ->field('id,name,pid,aliases,image,url')
            ->where($map)
            ->order('sort ASC, id DESC')
            ->cache(!APP_DEBUG)
            ->select();
            $result = $result->toArray();

            if (!empty($result)) {
                // 递归查询子类
                $_child = $this->queryChild($result);
                $result = !empty($_child) ? $_child : $result;
                $nav[$key]['child'] = $result;
            }
        }

        return $nav;
    }

    /**
     * 查询父导航
     * @access protected
     * @param  int $_id
     * @return int
     */
    protected function queryParent($_id)
    {
        $map = [
            ['id', '=', $_id],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->field('id,pid')
        ->where($map)
        ->order('sort ASC, id DESC')
        ->cache(!APP_DEBUG)
        ->find();

        $result = $result ? $result->toArray() : [];

        if (!empty($result['pid'])) {
            return $this->queryParent($result['pid']);
        }

        return $result ? $result['id'] : 0;
    }
}
