<?php
/**
 *
 * 缓存 - 内容 - 控制器
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
        $file_path = (array) glob(env('runtime_path') . 'temp' . DIRECTORY_SEPARATOR . '*');
        foreach ($file_path as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        // HTML静态缓存
        $file_path = (array) glob(env('runtime_path') . 'html' . DIRECTORY_SEPARATOR . '*');
        foreach ($file_path as $path) {
            $_path = (array) glob($path . DIRECTORY_SEPARATOR . '*');

            foreach ($_path as $pa) {
                $_pa = (array) glob($pa . DIRECTORY_SEPARATOR . '*');

                foreach ($_pa as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($pa);
            }
            rmdir($path);
        }

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
        DataCache::clear();

        $this->command();

        return true;
    }

    /**
     * 命令行生成缓存的文件
     * @access public
     * @param
     * @return void
     */
    public function command()
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
}
