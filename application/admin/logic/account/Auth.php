<?php
/**
 *
 * 登录 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\account;

class Auth
{

    /**
     * 权限菜单
     * @access public
     * @param
     * @return array
     */
    public function getMenu()
    {
        $auth_menu = [];
        if (session('?_access_list')) {
            $auth = session('_access_list');
            $auth = $auth['ADMIN'];

            $nav = lang('__nav');

            foreach ($auth as $key => $value) {
                $controller = strtolower($key);

                foreach ($value as $k => $val) {
                    $action = strtolower($k);

                    // 上传方法跳出不生成导航菜单
                    if ($action == 'upload') {
                        continue;
                    }

                    $auth_menu[$controller]['icon'] = config('app.icon.' . $controller);
                    $auth_menu[$controller]['name'] = $nav[$controller]['name'];
                    if (!empty($nav[$controller]['child'][$action])) {
                        $auth_menu[$controller]['menu'][] = [
                            'action' => $action,
                            'url'    => url($controller . '/' . $action, [], true, true),
                            'lang'   => $nav[$controller]['child'][$action],
                        ];
                    }
                }
            }
        }
        return $auth_menu;
    }

    /**
     * 网站标题与面包屑
     * @access public
     * @param
     * @return array
     */
    public function getTitBre()
    {
        $controller = strtolower(request()->controller());
        $action     = request()->action();

        $result = [
            'title'      => $this->getWebSiteTitle(),
            'breadcrumb' => $this->getBreadcrumb(),
            'sub_title'  => '',
        ];
        if ('upload' == $action) {
            // 上传方法
            $result['sub_title'] = lang('upload file');
        } else {
            $nav = lang('__nav');
            if (!empty($nav[$controller]['child'][$action])) {
                $result['sub_title'] = $nav[$controller]['child'][$action];
            }
        }
        return $result;
    }

    /**
     * 网站标题
     * @access private
     * @param
     * @return string
     */
    private function getWebSiteTitle()
    {
        $nav = lang('__nav');

        $controller = strtolower(request()->controller());
        $action     = request()->action();

        if (in_array($action, ['login', 'logout'])) {
            // 登录注销方法
            $title =  lang('admin login') . ' - NIPHPCMS';
        } elseif ('upload' == $action) {
            // 上传方法
           $title = lang('upload file') . ' - NIPHPCMS';
        } else {
            $title = $nav[$controller]['child'][$action];
            $title .= ' - ' . $nav[$controller]['name'] . ' - NIPHP';
        }

        return $title;
    }

    /**
     * 面包屑
     * @access private
     * @param
     * @return string
     */
    private function getBreadcrumb()
    {
        $nav = lang('__nav');

        $controller = strtolower(request()->controller());
        $action     = request()->action();

        if (in_array($action, ['login', 'logout'])) {
            return ;
        }

        $breadcrumb  = '<li><a href="' . url('settings/info') . '">';
        $breadcrumb .= lang('website home') . '</a></li>';

        $breadcrumb .= '<li><a href="';
        $breadcrumb .= url(
            $controller . '/' . current(array_keys($nav[$controller]['child']))
        );
        $breadcrumb .= '">' . $nav[$controller]['name'] . '</a></li>';

        if ('upload' == $action) {
            // 上传方法
            $breadcrumb .= '<li><a>';
            $breadcrumb .= lang('upload file');
            $breadcrumb .= '</a></li>';
        } else {
            $breadcrumb .= '<li><a href="';
            $breadcrumb .= url($controller . '/' . $action) . '">';
            $breadcrumb .= $nav[$controller]['child'][$action];
            $breadcrumb .= '</a></li>';
        }

        if (input('param.cid/f')) {
            $bread = $this->getBreadcrumbParent(input('param.cid/f'));
        } elseif (input('param.pid/f')) {
            $bread = $this->getBreadcrumbParent(input('param.pid/f'));
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
     * @access private
     * @param  intval $_parent_id 父ID
     * @return intval
     */
    private function getBreadcrumbParent($_parent_id)
    {
        $action = request()->action();

        // 如果是内容类请求操作指定到栏目模型
        if ($action == 'content') {
            $action = 'category';
        }

        $result =
        model('common/' . $action)
        ->field(['id','pid','name'])
        ->where([
            ['id', '=', $_parent_id],
            ['lang', '=', lang(':detect')],
        ])
        ->find();

        $cate_data = !empty($result) ? $result : null;

        if (!empty($cate_data['pid'])) {
            $breadcrumb = $this->getBreadcrumbParent($cate_data['pid']);
        }

        $breadcrumb[] = $cate_data;
        return $breadcrumb;
    }
}
