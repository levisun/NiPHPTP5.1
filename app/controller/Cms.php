<?php
/**
 *
 * 控制层
 * 调度控制器
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
use think\exception\HttpException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Request;
use app\library\Siteinfo;
use app\library\Template;

class Cms
{

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {
        if (isWechat() && Request::subDomain() !== 'wechat') {
            $url = Request::scheme() . '://wechat.' . Request::rootDomain() . Request::root();
            echo Response::create($url, 'redirect', 302)->send();
            die();
        }

        elseif (Request::isMobile() && Request::subDomain() !== 'm') {
            $url = Request::scheme() . '://m.' . Request::rootDomain() . Request::root();
            echo Response::create($url, 'redirect', 302)->send();
            die();
        }
    }

    /**
     * CMS
     * @param  string $name 分层名
     * @param  int    $cid  栏目ID
     * @param  int    $id   文章ID
     * @return mixed        HTML文档
     */
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
}
