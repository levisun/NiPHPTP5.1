<?php
/**
 *
 * 删除运行垃圾文件 - 方法库
 *
 * @package   NiPHP
 * @category  app\common\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\library;

use think\App;
use think\facade\Env;
use think\facade\Request;

class Garbage
{

    public function handle($event, App $app): void
    {
        $this->remove();
    }

     /**
     * 删除运行垃圾文件
     * @access public
     * @param
     * @return void
     */
    public function remove(): bool
    {
        // 减少频繁操作,每次请求百分之一几率运行操作
        if (rand(1, 100) === 1) {
            return false;
        }

        $files = [
            Env::get('runtime_path') . 'cache' . DIRECTORY_SEPARATOR,
            Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR,
            Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR. Request::app() . DIRECTORY_SEPARATOR,
        ];

        $dirOrPath = [];
        foreach ($files as $dir) {
            $dirOrPath = array_merge($dirOrPath, (array) glob($dir. '*'));
        }

        $dirOrPath = $this->getAll($dirOrPath);

        // 为空
        if (!empty($dirOrPath)) {
            // 随机抽取1000条信息
            shuffle($dirOrPath);
            $dirOrPath = array_slice($dirOrPath, 0, 1000);

            foreach ($dirOrPath as $path) {
                if (is_file($path)) {
                    @unlink($path);
                } elseif (is_dir($path)) {
                    @rmdir($path);
                }
            }
        }

        return true;
    }

    /**
     * 获得目录中的所有文件与目录
     * @access private
     * @param  string $_dirOrPath
     * @return array
     */
    private function getAll($_dirOrPath): array
    {
        $days = strtotime('-3 days');

        $allFiles = [];
        foreach ($_dirOrPath as $key => $path) {
            if (is_file($path)) {
                // 过滤未过期文件
                if (filectime($path) <= $days) {
                    $allFiles[] = $path;
                }
            } elseif (is_dir($path . DIRECTORY_SEPARATOR)) {
                $temp = (array) glob($path . DIRECTORY_SEPARATOR . '*');
                if (!empty($temp)) {
                    $temp = $this->getAll($temp);
                    $allFiles = array_merge($allFiles, $temp);
                    unset($temp);
                } else {
                    $allFiles[] = $path;
                }
            }
        }

        return $allFiles;
    }
}
