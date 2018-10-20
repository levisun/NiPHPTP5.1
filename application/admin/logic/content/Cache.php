<?php
/**
 *
 * 缓存 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\content;

use think\facade\Cache as DataCache;

class Cache
{

    /**
     * 编译与HTML静态缓存文件
     * @access public
     * @param
     * @return boolean
     */
    public function compile()
    {
        // 编译缓存
        $this->removeHtml();

        return true;
    }

    /**
     * 数据缓存的文件
     * @access public
     * @param
     * @return boolean
     */
    public function cache()
    {
        $dir_path = (array) glob(env('runtime_path') . 'cache' . DIRECTORY_SEPARATOR . '*');
        $all_files = $this->getDir($dir_path);
        if (!empty($all_files)) {
            foreach ($all_files as $path) {
                if (is_file($path)) {
                    @unlink($path);
                } elseif (is_dir($path)) {
                    @rmdir($path);
                }
            }
        }

        $this->removeCommand();
        $this->removeHtml();

        return true;
    }

    /**
     * HTML静态文件
     * @access private
     * @param
     * @return void
     */
    private function removeHtml()
    {
        // 静态文件
        $dir_path = (array) glob(env('root_path') . 'public' . DIRECTORY_SEPARATOR .  'html' . DIRECTORY_SEPARATOR . '*');
        $all_files = $this->getDir($dir_path);
        if (!empty($all_files)) {
            foreach ($all_files as $path) {
                if (is_file($path)) {
                    @unlink($path);
                } elseif (is_dir($path)) {
                    @rmdir($path);
                }
            }
        }

        // 编译文件
        $dir_path = (array) glob(env('runtime_path') . 'temp' . DIRECTORY_SEPARATOR . '*');
        $all_files = $this->getDir($dir_path);
        if (!empty($all_files)) {
            foreach ($all_files as $path) {
                if (is_file($path)) {
                    @unlink($path);
                } elseif (is_dir($path)) {
                    @rmdir($path);
                }
            }
        }
    }

    /**
     * 命令行生成缓存的文件
     * @access private
     * @param
     * @return void
     */
    private function removeCommand()
    {
        $file_path = [];

        // 配置缓存
        $file_path[] = env('runtime_path') . 'init.php';
        $file_path[] = env('runtime_path') . 'admin' . DIRECTORY_SEPARATOR . 'init.php';
        $file_path[] = env('runtime_path') . 'cms' . DIRECTORY_SEPARATOR . 'init.php';
        $file_path[] = env('runtime_path') . 'mall' . DIRECTORY_SEPARATOR . 'init.php';
        $file_path[] = env('runtime_path') . 'user' . DIRECTORY_SEPARATOR . 'init.php';
        $file_path[] = env('runtime_path') . 'wechat' . DIRECTORY_SEPARATOR . 'init.php';

        // 数据表字段缓存
        // $file_path[] = (array) glob(env('runtime_path') . 'schema' . DIRECTORY_SEPARATOR . '*');

        // 路由映射缓存
        $file_path[] = env('runtime_path') . 'route.php';

        foreach ($file_path as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * 获得目录中的所有文件与目录
     * @access private
     * @param  string $_dir_path
     * @return array
     */
    private function getDir($_dir_path)
    {
        $all_files = [];
        foreach ($_dir_path as $key => $path) {
            if (is_file($path)) {
                $all_files[] = $path;
            } elseif (is_dir($path . DIRECTORY_SEPARATOR)) {
                $temp = (array) glob($path . DIRECTORY_SEPARATOR . '*');
                if (!empty($temp)) {
                    $temp = $this->getDir($temp);
                    $all_files = array_merge($all_files, $temp);
                } else {
                    $all_files[] = $path;
                }
            }
        }

        return $all_files;
    }
}
