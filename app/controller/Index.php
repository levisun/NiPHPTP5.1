<?php
namespace app\controller;

use think\exception\HttpException;
use app\server\Tpl;

class Index
{

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {

    }

    public function index(string $name = 'index')
    {
        return (new Tpl)->fetch($name);
    }

    public function abort(int $code = 404)
    {
        throw new HttpException($code);
    }
}
