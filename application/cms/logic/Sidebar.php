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
    public function query($_cid = 0)
    {
        $_cid = input('param.cid/f', (float) $_cid);

        $id = $this->queryParent($_cid);

        $map = [
            ['id', '=', $id],
            ['is_show', '=', 1],
            ['pid', '=', 0],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->field('id,name,pid,aliases,image,url,is_channel,model_id')
        ->where($map)
        ->order('sort ASC, id DESC')
        ->cache(!APP_DEBUG)
        ->select()
        ->toArray();

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
        $nav = [];

        foreach ($_data as $key => $value) {
            $nav[$key] = $value;
            $nav[$key]['url'] = logic('cms/nav')->getUrl($value['model_id'], $value['is_channel'], $value['id']);

            $result =
            model('common/category')
            ->field('id,name,pid,aliases,image,url,is_channel,model_id')
            ->where([
                ['lang', '=', lang(':detect')],
                ['pid', '=', $value['id']]
            ])
            ->order('sort ASC, id DESC')
            ->cache(!APP_DEBUG)
            ->select()
            ->toArray();

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
        $result =
        model('common/category')
        ->field('id,pid')
        ->where([
            ['id', '=', $_id],
            ['lang', '=', lang(':detect')],
        ])
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
