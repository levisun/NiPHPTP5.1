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
        $result = $this->run();

        if ($result === false) {
            $this->error($this->errorMsg);
        } elseif ($result === null) {
            $this->error('404', 'ABORT:404');
        } else {
            $this->success('QUERY SUCCESS', $result);
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
        $this->success('IP INFO', logic('common/IpInfo')->getInfo());
    }

    protected function auth()
    {
        return true;
    }
}
