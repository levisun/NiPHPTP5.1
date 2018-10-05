<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\cms\controller;

use app\common\logic\Async;

class Api extends Async
{

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        $result = $this->exec();
        if ($result === false) {
            return $this->outputError($this->errorMsg);
        } else {
            return $this->outputData('QUERY SUCCESS', $result);
        }
    }

    /**
     * ip地区信息
     * @access public
     * @param
     * @return json
     */
    public function getipinfo()
    {

        return $this->outputData('IP INFO', logic('common/IpInfo')->getInfo());

    }

    /**
     * 验证异步加密签名
     * @access protected
     * @param
     * @return mixed
     */
    // protected function checkSign()
    // {
    //     return true;
    // }

    /**
     * 验证请求时间戳
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkTimestamp()
    {
        return true;
    }
}
