<?php
/**
 *
 * 公众数据 - 公众 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\common
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Common.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\common;

class Common
{
    // 不获得系统设置数据与权限菜单的方法
    protected $_action = [
        'login',
        'logout',
    ];

    // 控制器默认方法
    protected $bn = [
        'Settings' => 'info',
        'Theme'    => 'template',
        'Category' => 'category',
        'Content'  => 'content',
        'User'     => 'member',
        'Wechat'   => 'keyword',
        'Mall'     => 'goods',
        'Book'     => 'book',
        'Expand'   => 'log',
    ];

    // 请求参数
    protected $requestParam = [];
    // 菜单语言包
    protected $navAndMenu = [];

    public function __construct()
    {
        $this->requestParam = [
            // 请求模块
            'module'     => request()->module(),
            // 请求控制器
            'controller' => request()->controller(),
            // 请求方法
            'action'     => request()->action(),
            // 语言
            'lang'       => lang(':detect'),
            // 请求参数
            'param'      => [
                'cid' => request()->param('cid'),
                'pid' => request()->param('pid'),
            ],
        ];

        $this->navAndMenu = [
            'nav'  => lang('_nav'),
            'menu' => lang('_menu'),
        ];
    }

    /**
     * 生成系统数据
     * @access public
     * @param
     * @return array
     */
    public function createSysData()
    {
        $auth_data = [];
        if (in_array($this->requestParam['action'], $this->_action)) {
            $auth_data = ['title' => $this->getWebSiteTitle()];
        } else {
            $auth_data = [
                // 管理员数据
                'admin_data' => session('admin_data'),
                // 权限菜单
                'auth_menu'  => $this->getAuthMenu(),
                // 系统标题
                'title'      => $this->getWebSiteTitle(),
                // 面包屑
                'breadcrumb' => $this->getBreadcrumb(),

            ];

            if ('upload' == $this->requestParam['action']) {
                // 上传方法
                $auth_data['sub_title'] = lang('upload file');
            } else {
                $auth_data['sub_title'] = $this->navAndMenu['menu'][strtolower($this->requestParam['controller'] . '_' . $this->requestParam['action'])];
            }
        }
        return $auth_data;
    }

    /**
     * 面包屑
     * @access protected
     * @param
     * @return array
     */
    protected function getBreadcrumb()
    {
        $breadcrumb = '<li><a href="' . url('settings/info') . '">';
        $breadcrumb .= lang('website home') . '</a></li>';

        $breadcrumb .= '<li><a href="';
        $breadcrumb .= url($this->requestParam['controller'] . '/' . $this->bn[$this->requestParam['controller']]);
        $breadcrumb .= '">' . $this->navAndMenu['nav'][strtolower($this->requestParam['controller'])] . '</a></li>';


        if ('upload' == $this->requestParam['action']) {
            // 上传方法
            $breadcrumb .= '<li><a>';
            $breadcrumb .= lang('upload file');
            $breadcrumb .= '</a></li>';
        } else {
            $breadcrumb .= '<li><a href="';
            $breadcrumb .= url($this->requestParam['controller'] . '/' . $this->requestParam['action']) . '">';
            $breadcrumb .= $this->navAndMenu['menu'][strtolower($this->requestParam['controller'] . '_' . $this->requestParam['action'])];
            $breadcrumb .= '</a></li>';
        }


        if (request()->param('cid')) {
            $bread = $this->getBreadcrumbParent(request()->param('cid'));
        } elseif (request()->param('pid')) {
            $bread = $this->getBreadcrumbParent(request()->param('pid'));
        }

        if (!empty($bread)) {
            $count = count($bread);
            foreach ($bread as $key => $value) {
                if ($key+1 == $count) {
                    $breadcrumb .= '<li class="active"><a>' . $value['name'] . '</a></li>';
                } else {
                    $breadcrumb .= '<li><a href="' . url('content/content', ['pid' => $value['id']]) . '">' . $value['name'] . '</a></li>';
                }
            }
        }

        return $breadcrumb;
    }

    /**
     * 获得面包屑父级栏目
     * @access protected
     * @param  intval $parent_id 父ID
     * @return intval
     */
    protected function getBreadcrumbParent($parent_id)
    {
        $map = [
            ['id', '=', $parent_id],
            ['lang', '=', lang(':detect')],
        ];

        // 实例化栏目表模型
        $category = model('Category');

        $result =
        $category->field(['id','pid','name'])
        ->where($map)
        ->find();

        $cate_data = !empty($result) ? $result : null;

        if (!empty($cate_data['pid'])) {
            $breadcrumb = $this->getBreadcrumbParent($cate_data['pid']);
        }

        $breadcrumb[] = $cate_data;
        return $breadcrumb;
    }

    /**
     * 网站标题
     * @access protected
     * @param
     * @return array
     */
    protected function getWebSiteTitle()
    {
        if (in_array($this->requestParam['action'], $this->_action)) {
            // 登录注销方法
            $title =  lang('admin login') . ' - NIPHPCMS';
        } elseif ('upload' == $this->requestParam['action']) {
            // 上传方法
           $title = lang('upload file') . ' - NIPHPCMS';
        } else {
            $title = $this->navAndMenu['menu'][strtolower($this->requestParam['controller'] . '_' . $this->requestParam['action'])];
            $title .= ' - ' . $this->navAndMenu['nav'][strtolower($this->requestParam['controller'])] . ' - NIPHPCMS';
        }

        return $title;
    }

    /**
     * 权限菜单
     * @access protected
     * @param
     * @return array
     */
    protected function getAuthMenu()
    {
        $auth_menu = [];
        if (session('?_access_list')) {
            $auth = session('_access_list');
            $auth = $auth[strtoupper($this->requestParam['module'])];

            foreach ($auth as $key => $value) {
                $controller = strtolower($key);

                foreach ($value as $k => $val) {
                    $action = strtolower($k);

                    // 上传方法跳出不生成导航菜单
                    if ($action == 'upload') {
                        continue;
                    }

                    $auth_menu[$controller]['icon'] = config('app.icon.' . $controller);
                    $auth_menu[$controller]['name'] = $this->navAndMenu['nav'][$controller];
                    $auth_menu[$controller]['menu'][] = [
                        'action' => $action,
                        'url'    => url($controller . '/' . $action),
                        'lang'   => $this->navAndMenu['menu'][$controller . '_' . $action],
                    ];
                }
            }
        }
        return $auth_menu;
    }
}
