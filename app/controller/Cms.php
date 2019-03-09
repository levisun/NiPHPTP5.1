<?php
/**
 *
 * 控制层
 * CMS
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
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\library\Siteinfo;
use app\library\Template;
use app\model\Category as ModelCategory;

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
            $response = Response::create($url, 'redirect', 302);
            throw new HttpResponseException($response);
        }

        elseif (Request::isMobile() && Request::subDomain() !== 'm') {
            $url = Request::scheme() . '://m.' . Request::rootDomain() . Request::root();
            $response = Response::create($url, 'redirect', 302);
            throw new HttpResponseException($response);
        }
    }

    /**
     * CMS
     * @access public
     * @param
     * @return mixed        HTML文档
     */
    public function index()
    {
        return (new Template)->fetch('index');
    }

    /**
     * 列表页
     * @access public
     * @param  string $name 分层名
     * @param  int    $cid  栏目ID
     * @return mixed        HTML文档
     */
    public function catalog(string $name = 'article', int $cid = 0)
    {
        return (new Template)->fetch('list_' . $name);
    }

    /**
     * 详情页
     * @access public
     * @param  string $name 分层名
     * @param  int    $cid  栏目ID
     * @param  int    $id   文章ID
     * @return mixed        HTML文档
     */
    public function details(string $name = 'article', int $cid = 0, int $id = 0)
    {
        return (new Template)->fetch('details_' . $name);
    }
}
