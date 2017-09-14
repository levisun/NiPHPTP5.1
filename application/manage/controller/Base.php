<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Base.php v1.0.1 $
 * @link      http://www.NiPHP.com
 * @since     2016/10/22
 */
namespace app\manage\controller;

use think\Controller;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Config;

class Base extends Controller
{
	// 权限数据
	protected $authData = [];

	protected function initialize()
	{
		# code...
	}

	/**
	 * 权限
	 * @access protected
	 * @param
	 * @return void
	 */
	private function auth()
	{
		# code...
	}

	/**
	 * 加载语言包
	 * @access protected
	 * @param
	 * @return void
	 */
	private function lang()
	{
		$lang_path  = Env::get('app_path') . $this->request->module();
        $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
        $lang_paht .= Lang::detect() . DIRECTORY_SEPARATOR;

        // 加载全局语言包
        Lang::load($lang_path . Lang::detect() . '.php');

        // 加载对应语言包
        $lang_name  = strtolower($this->request->controller()) . '_';
        $lang_name .= strtolower($this->request->action());
        Lang::load($lang_path . $lang_name . '.php');
	}

	/**
	 * 模版设置
	 * @access protected
	 * @param
	 * @return void
	 */
	private function theme()
	{
		$view_path  = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
		$view_path .= 'theme' . DIRECTORY_SEPARATOR . $this->request->module();
		$view_path .= Config::get('default_theme') . DIRECTORY_SEPARATOR;

		$template = Config::get('template');
        $template['view_path'] = $view_path;
        $this->view->engine($template);

        // 默认跳转页面对应的模板文件
        $dispatch = [
            'dispatch_success_tmpl' => $view_path . 'dispatch_jump.html',
            'dispatch_error_tmpl'   => $view_path . 'dispatch_jump.html',
        ];
        Config::set($dispatch);

        // 获得域名地址
        $domain  = $this->request->domain();
        $domain .= substr($this->request->baseFile(), 0, -10);
        $default_theme  = $domain . '/public/theme/' . $this->request->module();
        $default_theme .= '/'. Config::get('default_theme') . '/';

        $replace = [
            '__DOMAIN__'   => $domain,
            '__PHP_SELF__' => basename($this->request->baseFile()),
            '__STATIC__'   => $domain . '/public/static/',
            '__THEME__'    => Config::get('default_theme'),
            '__CSS__'      => $default_theme . 'css/',
            '__JS__'       => $default_theme . 'js/',
            '__IMG__'      => $default_theme . 'images/',
        ];

        // 注入常用模板变量
        if (!empty($this->authData['auth_menu'])) {
            $replace['__ADMIN_DATA__'] = $this->authData['admin_data'];
            $replace['__MENU__']       = $this->authData['auth_menu'];
            $replace['__SUB_TITLE__']  = $this->authData['sub_title'];
            $replace['__BREADCRUMB__'] = $this->authData['breadcrumb'];
        }
        $replace['__TITLE__'] = $this->authData['title'];

        $this->view->replace($replace);

        $this->assign('button_search', 0);
        $this->assign('button_added', 0);
	}

	/**
     * 数据合法验证
     * @access protected
     * @param  string $validate_name 验证器名
     * @return mexid                 返回true or false or 提示信息
     */
	protected function illegal($validate_name)
	{
		if ($this->request->isPost()) {
            $data = $this->request->post();
        } else {
            $data = ['id' => $this->request->param('id/f')];
        }

		$result = $this->validate($data, $validate_name);
        if (true !== $result) {
            $this->error($result);
        }
	}
}
