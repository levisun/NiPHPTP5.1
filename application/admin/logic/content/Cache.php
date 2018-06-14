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

class Cache
{

    public function compile()
    {
        die();
    }

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
        $file_path[] = (array) glob(env('runtime_path') . 'schema' . DIRECTORY_SEPARATOR . '*');

        // 路由映射缓存
        $file_path[] = env('runtime_path') . 'route.php';
    }
}
