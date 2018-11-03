<?php
/**
 *
 * 导航 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\book\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\logic;

class Nav
{

    /**
     * 查询导航
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        return
        model('common/BookType')
        ->select();
    }

    /**
     * 查询子导航
     * @access protected
     * @param  array $data
     * @return array
     */
    protected function queryChild($_data)
    {

    }

    /**
     * 获得导航指向地址
     * Breadcrumb.php Sidebar.php 调用
     * @access public
     * @param  int    $_model_id   模型ID
     * @param  int    $_is_channel 是否频道页
     * @param  int    $_cat_id     导航ID
     * @return string
     */
    public function getUrl($_model_id, $_is_channel, $_cat_id)
    {

    }
}
