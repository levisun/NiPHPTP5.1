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
        if (!APP_DEBUG && !request()->isAjax() && !request()->isPjax() && !request()->isPost()) {
            if (request()->module() == 'admin' && request()->action() != 'login' && !session('?' . config('user_auth_key'))) {
                header('Location:' . url('/admin'));
                exit();
            }

            $path = $this->htmlPath();

            if (is_file($path) && filectime($path) >= time() - config('cache.expire')) {
                // 异步请求
                logic('common/async')->createRequest();

                $html = file_get_contents($path);
                $html = preg_replace('/<\?php(.*?)\?>/si', '', $html);

                // 替换新的表单令牌
                $html = preg_replace('/(<input type="hidden" name="__token__" value=").*?(" \/>)/si', token(), $html);

                echo $html;
                echo '<script type="text/javascript">console.log("本页面由 NIPHP 负责开发，你可以通过 http://niphp.com 了解我们。\r\n' . implode(use_time_memory(), '\r\n') . '")</script>';
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
        if (!APP_DEBUG && !request()->isAjax() && !request()->isPjax() && !request()->isPost()) {
            $path = $this->htmlPath();

            if (!APP_DEBUG && is_file($path) && filectime($path) >= time() - config('cache.expire')) {
                unlink($path);
                return true;
            }

            $storage = new \think\template\driver\File;

            if (is_wechat_request()) {
                $request_type = 'WECHAT';
            } elseif (request()->isMobile()) {
                $request_type = 'MOBILE';
            } else {
                $request_type = 'PC';
            }

            $_content = "<?php\r/*\r" . date('Y-m-d H:i:s') . "\rrequest " . $request_type . "\r" . request()->url(true) . "\r*/\rexit();\r?>\r" . $_content;
            $_content .= '<script type="text/javascript">console.log("HTML ' . $request_type . '端静态缓存 生成日期' . date('Y-m-d H:i:s') . '")</script>';

            $storage->write($path, $_content);
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

        $file_name = request()->url(true) . $user_id;

        if (is_wechat_request()) {
            $file_name .= 'wechat';
        } elseif (request()->isMobile()) {
            $file_name .= 'mobile';
        } else {
            $file_name .= 'pc';
        }

        $file_name = md5($file_name);

        $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
        $html_path .= substr($file_name, 0, 2) . DIRECTORY_SEPARATOR;
        $html_path .= substr($file_name, 2) . '.php';

        return $html_path;
    }
}
