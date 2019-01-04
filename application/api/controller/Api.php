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

class Api
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
