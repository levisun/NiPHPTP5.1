<?php
/**
 *
 * 幻灯片 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Banner
{

    /**
     * 查询广告
     * @access public
     * @param  int $data
     * @return array
     */
    public function query($_slide_id = 0)
    {
        $parent =
        model('common/banner')
        ->field(true)
        ->where([
            ['id', '=', input('param.slide_id/f', $_slide_id)],
            ['lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG)
        ->find();

        if (is_null($parent)) {
            return null;
        }

        $result =
        model('common/banner')
        ->field(true)
        ->where([
            ['pid', '=', $parent['id']],
            ['lang', '=', lang(':detect')],
        ])
        ->cache(!APP_DEBUG)
        ->select();

        $data = [];
        foreach ($result as $value) {
            // $value = $value->toArray();
            $value['url'] = url('/banner/' . $vo['id']);
            $value['width'] = $parent['width'];
            $value['height'] = $parent['height'];
            $data[] = $value;
        }

        return $data;
    }
}
