<?php
/**
 *
 * HTML静态 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */
namespace app\common\logic;

use think\template\driver\File;
use think\Response;
use think\exception\HttpResponseException;

class Html
{

    /**
     * 创建静态文件
     * @access public
     * @param  string $_content
     * @return void
     */
    public function write($_content)
    {
        $path = $this->htmlPath();
        if (!is_file($path) || filectime($path) <= strtotime('-1 days')) {
            $storage = new File;
            $storage->write($path, $_content);

            $this->forWrite($_content);
        }


    }

    /**
     * 生成当前页面中的所有静态页
     * @access private
     * @param  string $_content
     * @return void
     */
    private function forWrite($_content)
    {
        preg_match_all('/(<a.*? href=[\'|"])(.*?)([\'|"])/si', $_content, $matches);
        $matches = $matches[2];
        $matches = array_unique($matches);
        $matches = array_filter($matches);
        // print_r($matches);die();
        foreach ($matches as $key => $value) {
            file_get_contents($value);
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
        $path = request()->path();
        $path = $path ? $path : 'index';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path) . '.html';
        if (request()->module() !== 'cms') {
            $path = request()->module() . DIRECTORY_SEPARATOR . $path;
        }
        if (is_wechat_request()) {
            $path = 'wechat' . DIRECTORY_SEPARATOR . $path;
        } elseif (request()->isMobile()) {
            $path = 'mobile' . DIRECTORY_SEPARATOR . $path;
        }

        return
        env('root_path') . 'public' . DIRECTORY_SEPARATOR .
        'html' . DIRECTORY_SEPARATOR . $path;
    }
}
