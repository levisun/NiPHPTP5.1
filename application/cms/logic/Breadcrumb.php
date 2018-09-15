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
     * @param
     * @return array
     */
    public function query()
    {
        $_cid = input('param.cid/f', 0);

        if ($_cid) {
            return $this->queryParent($_cid);
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
        $result =
        model('common/category')
        ->field('id,name,pid,is_channel,model_id,seo_title,seo_keywords,seo_description')
        ->where([
            ['id', '=', $_id],
            ['lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG ? 'BREADCRUMB QUERYPARENT ID' . $_id : false)
        ->find();

        if ($result) {
            $result = $result->toArray();

            $result['url'] = logic('cms/nav')->getUrl($result['model_id'], $result['is_channel'], $result['id']);

            $parent = [];
            if (!empty($result['pid'])) {
                $parent = $this->queryParent($result['pid']);
            }

            $parent[] = $result;

            return $parent;
        }

        return [];
    }
}
