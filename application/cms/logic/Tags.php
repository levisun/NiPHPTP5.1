<?php
/**
 *
 * 标签 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Tags
{

    /**
     * 查询标签
     * @access public
     * @param  int $data
     * @return array
     */
    public function query()
    {
        $map = [
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/tags')
        ->field('id,name,count')
        ->where($map)
        ->cache(!APP_DEBUG)
        ->select();

        $result = $result->toArray();

        foreach ($result as $key => $value) {
            $result[$key]['url'] = url('/tags/' . $value['id']);
        }

        return $result;
    }
}
