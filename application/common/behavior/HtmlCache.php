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
 * @since     2018/9
 */

namespace app\common\behavior;

class HtmlCache
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

        if (request_block(['admin', 'member', 'wechat'])) {
            return false;
        }

        $path = $this->htmlPath();

        if (is_file($path) && filectime($path) >= time() - config('cache.expire')) {

            echo file_get_contents($path);

            \think\Facade\Hook::exec('app\\common\\behavior\\Visit');
            \think\Facade\Hook::exec('app\\common\\behavior\\RemoveRunGarbage');
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

        if (request_block(['admin', 'member', 'wechat'])) {
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

        $_content .= '<script type="text/javascript">console.log("' . $request_type . 'HTML静态缓存 生成日期' . date('Y-m-d H:i:s') . '");console.log("request url ' . request()->url(true) . '");</script>';

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
        /*$user_id  = session('?' . config('user_auth_key')) ? 'session=' . session(config('user_auth_key')) : '';
        $user_id .= cookie('?' . config('user_auth_key')) ? 'cookie=' . cookie(config('user_auth_key')) : '';

        $file_name = request()->url(true) . $user_id;*/

        # "public/"分割数组可能有错
        $path = request()->url();
        $path = explode('public/', $path);
        $path = !empty($path[1]) ? $path[1] : 'index.html';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        // 获得二级域名 根据二级域名生成文件路径
        $domain = substr(request()->url(true), 7, 2);

        if (is_wechat_request() || $domain == 'we') {
            $path = 'wechat' . DIRECTORY_SEPARATOR . $path;
        } elseif (request()->isMobile() || $domain == 'm.') {
            $path = 'mobile' . DIRECTORY_SEPARATOR . $path;
        }

        $html_path  = env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;


        $html_path .= $path;

        return $html_path;
    }
}
