<?php
/**
 *
 * 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/5
 */

namespace app\common\behavior;

class HtmlCacheBehavior
{

    /**
     * 加载静态文件
     * @access public
     * @param  string $_content
     * @return void
     */
    public function run()
    {
        if (APP_DEBUG) {
            return false;
        }

        if (request()->isAjax() || request()->isPjax() || request()->isPost()) {
            return false;
        }

        $module = strtolower(request()->module());
        if ($module === 'common') {
            return false;
        }

        if (in_array($module, ['admin', 'member', 'wechat'])) {
            return false;
        }

        $path = $this->htmlPath();

        if (is_file($path) && filectime($path) >= time() - config('cache.expire')) {
            // 异步请求
            logic('common/async')->createRequireToken();

            echo file_get_contents($path);
            exit();
        }
    }

    /**
     * 创建静态文件
     * @access public
     * @param  string $_content
     * @return void
     */
    public function write($_content)
    {
        if (APP_DEBUG) {
            return false;
        }

        if (request()->isAjax() || request()->isPjax() || request()->isPost()) {
            return false;
        }

        $module = strtolower(request()->module());
        if ($module === 'common') {
            return false;
        }

        if (in_array($module, ['admin', 'member', 'wechat'])) {
            return false;
        }

        $path = $this->htmlPath();
        if (is_file($path)) {
            unlink($path);
        }

        $storage = new \think\template\driver\File;

        if (is_wechat_request()) {
            $request_type = 'WECHAT';
        } elseif (request()->isMobile()) {
            $request_type = 'MOBILE';
        } else {
            $request_type = 'PC';
        }

        $_content .= '<script type="text/javascript">console.log("HTML ' . $request_type . '端静态缓存 生成日期' . date('Y-m-d H:i:s') . '");console.log("request url ' . request()->url(true) . '");</script>';

        $storage->write($path, $_content);
    }

    /**
     * 静态文件路径
     * 请求URL+session|cookie,MD5加密区别存储
     * @access private
     * @param
     * @return string
     */
    private function htmlPath()
    {
        $user_id  = session('?' . config('user_auth_key')) ? 'session=' . session(config('user_auth_key')) : '';
        $user_id .= cookie('?' . config('user_auth_key')) ? 'cookie=' . cookie(config('user_auth_key')) : '';

        $file_name = request()->url(true) . $user_id;

        $path = request()->url();
        $path = explode('public/', $path);
        $path = !empty($path[1]) ? $path[1] : 'index.html';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        if (is_wechat_request()) {
            $path = 'wechat' . DIRECTORY_SEPARATOR . $path;
        } elseif (request()->isMobile()) {
            $path = 'mobile' . DIRECTORY_SEPARATOR . $path;
        }

        $html_path  = env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;


        $html_path .= $path;

        return $html_path;
    }
}
