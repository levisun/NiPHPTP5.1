<?php
/**
 *
 * 网站信息 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\book\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\logic;

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
            $data[$value['name']] = $value['value'];
        }
        $data['title'] = $data['website_name'];

        if (input('param.bid/f')) {
            $res =
            model('common/book')
            ->field(['name', 'seo_title', 'seo_keywords', 'seo_description'])
            ->where([
                ['id', '=', input('param.bid/f')],
                ['is_show', '=', 1],
                ['is_pass', '=', 1],
            ])
            ->find()
            ->toArray();

            $data['website_name'] = $data['website_name'];

            if ($res['seo_title']) {
                $data['website_name'] = $res['seo_title'] . ' - ' . $data['website_name'];
            } else {
                $data['website_name'] = $res['name'] . ' - ' . $data['website_name'];
            }
        }

        if (input('param.id/f') && $result = logic('book/article')->query()) {
            $data['website_name'] = $result['title'] . ' - ' . $data['website_name'];

            if ($res['seo_keywords']) {
                $data['website_keywords'] = $res['seo_keywords'];
            } else {
                $data['website_keywords'] = $result['title'] . ',' . $res['name'];
            }

            if ($res['seo_description']) {
                $data['website_description'] = $res['seo_description'];
            } else {
                $data['website_description'] = $result['title'] . ',' . $res['name'];
            }
        }

        return $data;
    }
}
