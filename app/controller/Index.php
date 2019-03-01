<?php
namespace app\controller;

use think\exception\HttpException;
use think\facade\Config;
use app\library\Tpl;
use app\library\Template;

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

    public function index(string $name = 'index', int $cid = 0, int $id = 0)
    {
        if ($cid && $id) {
            $tpl_name = 'details_' . $name;
        } elseif ($cid) {
            $tpl_name = 'list_' . $name;
        } else {
            $tpl_name = $name;
        }
        return (new Template)->fetch($tpl_name);
    }

    public function abort(int $code = 404)
    {
        throw new HttpException($code);
    }
}
