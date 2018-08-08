<?php
/**
 *
 * 导航 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Nav
{

    /**
     * 查询导航
     * @access public
     * @param  int $_type_id
     * @return array
     */
    public function query($_type_id = 1)
    {
        $_type_id = input('param.type_id/f', $_type_id);

        $map = [
            ['type_id', '=', $_type_id],
            ['is_show', '=', 1],
            ['pid', '=', 0],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->field('id,name,pid,aliases,seo_title,seo_keywords,seo_description,image,url')
        ->where($map)
        ->order('sort ASC, id DESC')
        ->cache(!APP_DEBUG)
        ->select();

        $result = $result->toArray();

        return $this->queryChild($result);
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
            ->field('id,name,pid,aliases,seo_title,seo_keywords,seo_description,image,url')
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
}
