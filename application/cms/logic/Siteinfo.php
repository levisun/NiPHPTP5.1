<?php
/**
 *
 * 网站信息 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Siteinfo
{

    /**
     * 查询标签
     * @access public
     * @param  int $data
     * @return array
     */
    public function query()
    {
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script,cms_theme'],
            ['lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG ? __METHOD__ . lang(':detect') : false)
        ->select()
        ->toArray();

        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = htmlspecialchars_decode($value['value']);
        }
        $data['title'] = $data['website_name'];

        $result = logic('cms/breadcrumb')->query();
        foreach ($result as $key => $value) {
            $data['title'] = $value['name'] . ' - ' . $data['title'];
        }
        $result = end($result);
        if (!empty($result['seo_title'])) {
            $data['title'] = $result['seo_title'] . ' - ' . $data['title'];
        }
        if (!empty($result['seo_keywords'])) {
            $data['website_keywords'] = $result['seo_keywords'];
        }
        if (!empty($result['seo_description'])) {
            $data['website_description'] = $result['seo_description'];
        }

        return $data;
    }
}
