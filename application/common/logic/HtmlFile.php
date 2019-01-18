<?php
/**
 *
 * HTML静态 - 业务层
 *
 * @package   NiPHP
 * @category  application\common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */
namespace app\common\logic;

use think\template\driver\File;
use think\Response;
use think\exception\HttpResponseException;

class HtmlFile
{

    /**
     * 创建静态文件
     * @access public
     * @param  string $_content
     * @return void
     */
    public function write($_content)
    {
        $path = request()->path();
        $path = $path ? $path : 'index';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path) . '.html';
        $path = $this->htmlPath() . $path;

        $storage = new File;
        $storage->write($path, $_content);
    }

    /**
     * 重定向HTML静态地址
     * @access public
     * @param  string $_url
     * @return string
     */
    public function redirect($_url, $_module)
    {
        $_module = $_module ? $_module : request()->module();
        $days = APP_DEBUG ? strtotime('-20 minute') : strtotime('-1 hour');

        $_url = trim($_url, '/');
        $_url = str_replace('/', DIRECTORY_SEPARATOR, $_url);

        $path = $this->htmlPath($_module) . $_url;

        if (is_file($path) && filectime($path) >= strtotime($days)) {
            return '/html/' . $_module . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $_url);
        } else {
            return '/' . str_replace(DIRECTORY_SEPARATOR, '/', $_url);
        }
    }

    /**
     * 静态文件路径
     *
     * @access public
     * @param
     * @return string
     */
    public function htmlPath($_module = '')
    {
        $_module = $_module ? $_module : request()->module();
        $path = env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'html' .
                DIRECTORY_SEPARATOR . $_module. DIRECTORY_SEPARATOR;
        if (is_wechat_request()) {
            $path .= 'wechat' . DIRECTORY_SEPARATOR;
        } elseif (request()->isMobile()) {
            $path .= 'mobile' . DIRECTORY_SEPARATOR;
        }

        return $path;
    }
}
