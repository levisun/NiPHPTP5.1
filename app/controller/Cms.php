<?php
/**
 *
 * 控制层
 * CMS
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
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\library\Siteinfo;
use app\library\Template;
use app\model\Category as ModelCategory;

class Cms extends Template
{

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme = 'cms' . DIRECTORY_SEPARATOR . Siteinfo::theme() . DIRECTORY_SEPARATOR;
        $tpl_path = Config::get('app.cdn_host') . '/template/' . 'cms' . '/' . Siteinfo::theme() . '/';

        $this->templateReplace = [
            '{:__CSS__}'         => $tpl_path . 'css/',
            '{:__IMG__}'         => $tpl_path . 'img/',
            '{:__JS__}'          => $tpl_path . 'js/',
            '{:__STATIC__}'      => Config::get('app.cdn_host') . '/static/',
            '{:__TITLE__}'       => Siteinfo::title(),
            '{:__KEYWORDS__}'    => Siteinfo::keywords(),
            '{:__DESCRIPTION__}' => Siteinfo::description(),
            '{:__BOTTOM_MSG__}'  => Siteinfo::bottom(),
            '{:__COPYRIGHT__}'   => Siteinfo::copyright(),
            '{:__SCRIPT__}'      => Siteinfo::script(),
        ];

        // if (isWechat() && Request::subDomain() !== 'wechat') {
        //     $url = Request::scheme() . '://wechat.' . Request::rootDomain() . Request::root();
        //     $response = Response::create($url, 'redirect', 302);
        //     throw new HttpResponseException($response);
        // }

        // elseif (Request::isMobile() && Request::subDomain() !== 'm') {
        //     $url = Request::scheme() . '://m.' . Request::rootDomain() . Request::root();
        //     $response = Response::create($url, 'redirect', 302);
        //     throw new HttpResponseException($response);
        // }
    }

    /**
     * CMS
     * @access public
     * @param
     * @return mixed HTML文档
     */
    public function index()
    {
        $this->fetch('index');
    }

    /**
     * 列表页
     * @access public
     * @param  string $name 分层名
     * @param  int    $cid  栏目ID
     * @return mixed        HTML文档
     */
    public function lists(string $name = 'article', int $cid = 0)
    {
        $this->fetch('list_' . $name);
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
        $this->fetch('details_' . $name);
    }
}
