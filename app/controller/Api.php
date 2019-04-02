<?php
/**
 *
 * 控制层
 * Api
 *
 * @package   NICMS
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
use think\facade\Request;
use app\library\Api as LibraryApi;

class Api extends LibraryApi
{

    /**
     * 查询接口
     * @access public
     * @param  string $name API分层名
     * @return void
     */
    public function query(string $name = 'cms')
    {
        if (Request::isGet()) {
            $this->setModule($name)->run();
        } else {
            $this->illegal();
        }
    }

    /**
     * 操作接口
     * @access public
     * @param  string $name API分层名
     * @return void
     */
    public function handle(string $name = 'cms')
    {
        if (Request::isPost()) {
            $this->setModule($name)->run();
        } else {
            $this->illegal();
        }
    }

    /**
     * 上传接口
     * @access public
     * @param
     * @return void
     */
    public function upload(string $name = 'cms')
    {
        if (Request::isPost() && !empty($_FILES)) {
            $this->setModule($name)->run();
        } else {
            $this->illegal();
        }
    }

    /**
     * 非法请求
     * @access private
     * @param
     * @return void
     */
    private function illegal()
    {
        $response = Response::create([
            'code'    => 'ERROR',
            'expire'  => date('Y-m-d H:i:s', time() + 30),
            'message' => Request::param('method') . ' does not have a method'
        ], 'json', 200);
        throw new HttpResponseException($response);
    }
}
