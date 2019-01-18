<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHP
 * @category  application\api\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\api\controller;

use app\common\logic\Async;

class Api extends Async
{

    public function index()
    {
        list($model, $action) = explode('/', request()->path(), 2);

        $logic = logic('api/' . $model);

        call_user_func_array([$logic, $action], []);
    }

    /**
     * 获得IP地址地区信息
     * @access public
     * @param
     * @return json
     */
    public function getipinfo()
    {
        $result = logic('common/IpInfo')->getInfo(input('param.ip'));
        $this->success('QUERY SUCCESS', $result);
    }

    /**
     * 访问记录
     * @access public
     * @param
     * @return void
     */
    public function visit()
    {
        (new \app\common\behavior\Visit)->run();
        die();
    }

    /**
     * 错误页面
     * @access public
     * @param
     * @return
     */
    public function abort()
    {
        abort(404);
    }
}
