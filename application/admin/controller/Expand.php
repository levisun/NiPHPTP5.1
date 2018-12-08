<?php
/**
 *
 * 扩展 - 控制器
 *
 * @package   NiPHP
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

class Expand extends Base
{

    /**
     * 系统日志
     * @access public
     * @param
     * @return mixed
     */
    public function log()
    {
        return $this->fetch();
    }

    /**
     * 数据库
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function databack($operate = '')
    {
        $tpl = $operate ? 'databack_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 错误日志
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function elog($operate = '')
    {
        $tpl = $operate ? 'elog_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 统计信息
     * @access public
     * @param
     * @return mixed
     */
    public function visit()
    {
        return $this->fetch();
    }
}
