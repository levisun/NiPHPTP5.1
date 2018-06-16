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
        $request =
        !request()->isAjax() &&
        !request()->isPjax() &&
        !request()->isPost();

        if (!APP_DEBUG && $request) {
            if (request()->module() == 'admin' && request()->action() != 'login' && !session('?' . config('user_auth_key'))) {
                header('Location:' . url('/admin'));
                exit();
            }

            $path = $this->htmlPath();

            if (is_file($path)) {
                // 异步请求加密签名
                logic('common/async')->createSign();

                $html = file_get_contents($path);

                // 替换新的表单令牌
                $html = preg_replace('/(<input type="hidden" name="__token__" value=").*?(" \/>)/si', token(), $html);

                echo $html;

                exit();
            }
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
        $request =
        !request()->isAjax() &&
        !request()->isPjax() &&
        !request()->isPost();

        if (!APP_DEBUG && $request) {
            $path = $this->htmlPath();

            if (is_file($path) && filectime($path) >= strtotime('-30 days')) {
                return true;
            }

            $storage = new \think\template\driver\File;
            $storage->write($this->htmlPath(), $_content);
        }
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

        $url = request()->url();
        $md5 = md5($url . $user_id);

        $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
        $html_path .= request()->module() . DIRECTORY_SEPARATOR;
        $html_path .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $html_path .= substr($md5, 2) . '.html';

        return $html_path;
    }
}
