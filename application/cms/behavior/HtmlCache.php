<?php
/**
 *
 * html缓存 - 行为
 *
 * @package   NiPHPCMS
 * @category  cms\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\cms\behavior;

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
            return true;
        }

        // 阻挡Ajax Pjax Post类型请求
        // 阻挡common模块请求
        if (request_block()) {
            return true;
        }

        $path = $this->htmlPath();

        if (is_file($path) && filectime($path) >= time() - config('cache.expire')) {

            behavior('app\\cms\\behavior\\Visit');
            behavior('app\\common\\behavior\\RemoveRunGarbage');

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
        if (request_block()) {
            return true;
        }

        $path = $this->htmlPath();
        if (is_file($path)) {
            unlink($path);
        }

        $storage = new \think\template\driver\File;
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
        # "public/"分割数组可能有错
        $path = request()->url();
        $path = explode('public/', $path);
        $path = !empty($path[1]) ? $path[1] : 'index.html';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        // 获得二级域名 根据二级域名生成文件路径
        // $domain = substr(request()->url(true), 7, 2);

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
