<?php
/**
 *
 * 面包屑 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Breadcrumb
{

    /**
     * 查询导航
     * @access public
     * @param  int $data
     * @return array
     */
    public function query()
    {
        $_cat_id = input('param.cid/f', 0);

        if ($_cat_id) {
            return $this->queryParent($_cat_id);
        } else {
            return [];
        }
    }

    /**
     * 查询父导航
     * @access protected
     * @param  int $data
     * @return array
     */
    protected function queryParent($_id)
    {
        $map = [
            ['id', '=', $_id],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->field('id,name,pid')
        ->where($map)
        ->cache(!APP_DEBUG)
        ->find();

        $result = $result->toArray();
        $result['url'] = url('/list/' . $result['id']);

        $parent = [];
        if (!empty($result['pid'])) {
            $parent = $this->queryParent($result['pid']);
        }

        $parent[] = $result;

        return $parent;
    }
}
