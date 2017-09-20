<?php
/**
 *
 * 公众数据 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Common.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\account;

use think\facade\Lang;

class Common
{
    protected $_action = [
        'login',
        'logout',
        'upload',
        'delupload',
    ];

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

    protected $request = [];
    protected $navAndMenu = [];

    public function __construct()
    {
        $this->request = [
            'module'     => request()->module(),
            'controller' => request()->controller(),
            'action'     => request()->action(),
            'lang'       => Lang::detect(),
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

    public function getSysData()
    {
        $auth_data = [];
        if (in_array($this->request['action'], $this->_action)) {
            $auth_data = ['title' => $this->getWebSiteTitle()];
        } else {
            $auth_data = [
                'admin_data' => session('admin_data'),
                'auth_menu'  => $this->getAuthMenu(),
                'title'      => $this->getWebSiteTitle(),
                'breadcrumb' => $this->getBreadcrumb(),
                'sub_title'  => $this->navAndMenu['menu'][
                    strtolower($this->request['controller'] . '_' . $this->request['action'])
                ]
            ];
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
        $breadcrumb .= url($this->request['controller'] . '/' . $this->bn[$this->request['controller']]);
        $breadcrumb .= '">' . $this->navAndMenu['nav'][strtolower($this->request['controller'])] . '</a></li>';

        $breadcrumb .= '<li><a href="';
        $breadcrumb .= url($this->request['controller'] . '/' . $this->request['action']) . '">';
        $breadcrumb .= $this->navAndMenu['menu'][strtolower($this->request['controller'] . '_' . $this->request['action'])];
        $breadcrumb .= '</a></li>';

        if (request()->param('cid')) {
            $bread = $this->getBreadcrumbParent(request()->param('cid'));
        }
        if (request()->param('pid')) {
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
            'id'   => $parent_id,
            'lang' => Lang::detect()
        ];

        $category = model('Category');

        $result =
        $category->field($field)
        ->where($map)
        ->column('id','pid','name');

        $cate_data = !empty($result) ? $result->toArray() : null;

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
        if (in_array($this->request['action'], $this->_action)) {
            if ('upload' == $this->request['action']) {
                return lang('upload file') . ' - NIPHPCMS';
            }
            return  lang('admin login') . ' - NIPHPCMS';
        }

        $title = $this->navAndMenu['menu'][strtolower($this->request['controller'] . '_' . $this->request['action'])];
        $title .= ' - ' . $this->navAndMenu['nav'][strtolower($this->request['controller'])] . ' - NIPHPCMS';
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
        if (!session('?_access_list')) {
            return false;
        }
        $auth = session('_access_list');
        $auth = $auth[strtoupper($this->request['module'])];
        $this->navAndMenu['nav'] = lang('_nav');
        $this->navAndMenu['menu'] = lang('_menu');
        $auth_menu = array();
        foreach ($auth as $key => $value) {
            $controller = strtolower($key);
            foreach ($value as $k => $val) {
                $action = strtolower($k);
                $auth_menu[$controller]['icon'] = config($k);
                $auth_menu[$controller]['name'] = $this->navAndMenu['nav'][$controller];
                $auth_menu[$controller]['menu'][] = [
                    'action' => $action,
                    'url'    => url($controller . '/' . $action),
                    'lang'   => $this->navAndMenu['menu'][$controller . '_' . $action],
                ];
            }
        }
        return $auth_menu;
    }
}
