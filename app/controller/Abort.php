<?php
/**
 *
 * 控制层
 * 错误
 *
 * @package   NiPHP
 * @category  app\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\controller;

use think\Response;
use think\exception\HttpResponseException;
use think\facade\Env;
use app\library\Siteinfo;

class Abort
{

    /**
     * 错误页
     * @access public
     * @param
     * @return mixed
     */
    public function error()
    {
        $this->tpl(404);
    }

    /**
     * 授权错误
     * @access public
     * @param
     * @return mixed
     */
    public function authority()
    {
        $response = Response::create($this->tpl($code), '', $code);
        return $response->send();
    }


    private function tpl(int $_code)
    {
        $tpl = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR .
                'template' . DIRECTORY_SEPARATOR .
                Siteinfo::theme() . DIRECTORY_SEPARATOR .
                $_code . '.html';

        if (!is_file($tpl)) {
            $tpl = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR .
                    'template' . DIRECTORY_SEPARATOR .
                    $_code . '.html';
        }

        $tpl = file_get_contents($tpl);
        $response = Response::create($tpl, '', $_code);
        throw new HttpResponseException($response);
    }
}
