<?php
/**
 *
 * 访问统计 - 扩展 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\expand
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\expand;

class Visit
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        if (input('param.operate', 'visit') == 'visit') {
            return $this->visit();
        } else {
            return $this->searchengine();
        }
    }

    /**
     * 搜索引擎日志
     * @access public
     * @param
     * @return array
     */
    public function searchengine()
    {
        // 删除过期的统计(保留三个月)
        model('common/Searchengine')
        ->where([
            ['date', '<=', strtotime('-90 days')]
        ])
        ->delete();

        $result =
        model('common/Searchengine')
        ->order('date DESC')
        ->paginate();

        $list = $result->toArray();
        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['date'] = date('Y-m-d', $value['date']);
        }

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
        ];
    }

    /**
     * 访问日志
     * @access public
     * @param
     * @return array
     */
    private function visit()
    {
        // 删除过期的统计(保留三个月)
        model('common/Visit')
        ->where([
            ['date', '<=', strtotime('-90 days')]
        ])
        ->delete();

        $result =
        model('common/Visit')
        ->order('date DESC')
        ->paginate();

        $list = $result->toArray();
        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['date'] = date('Y-m-d', $value['date']);
        }

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
        ];
    }
}
