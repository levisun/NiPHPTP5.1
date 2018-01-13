<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Api.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Env;

class Api extends Controller
{
    protected $receiveParam = [];

    public function recapi()
    {
        $rec = input('post.');

        $method = input('param.method');

        $result = $this->run($method);

        return json([
            'method'   => $method,
            'params'   => $rec,
            'return'   => $result === false ? [] : $result,
            'errorMsg' => $result === false ? 'illegal' : 'success'
        ]);
    }

    /**
     * 判断API方法是否存在并执行
     * @access private
     * @param  string  $_method
     * @return mixed
     */
    private function run($_method)
    {
        list($logic, $action, $layer) = explode('-', $_method);

        $file = Env::get('app_path') . DIRECTORY_SEPARATOR .
        $this->request->module() . DIRECTORY_SEPARATOR .
        'logic' . DIRECTORY_SEPARATOR .
        $layer . DIRECTORY_SEPARATOR . $logic . '.php';

        $sign = cookie('?__sign') ? decrypt(cookie('__sign')) + 12 : false;

        if ($sign && $sign >= time() && is_file($file)) {
            // 给业务模型指定模块
            $logic = $this->request->module() . '/' . $logic;

            $api_logic = logic($logic, $layer);

            if (method_exists($api_logic, $action)) {
                return $api_logic->$action();
            }
        }

        return false;
    }
}
