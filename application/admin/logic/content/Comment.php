<?php
/**
 *
 * 评论 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/6
 */
namespace app\admin\logic\content;

class Comment
{
    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/comment')
        ->where([
            ['lang', '=', lang(':detect')],
        ])
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url('content/comment'),
        ]);

        $list = $result->toArray();

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
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {}

    /**
     * 删除
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {}

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {}

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {}
}
