<?php
/**
 *
 * 清理运行垃圾 - 行为
 * 过期的数据缓存垃圾,模板编译垃圾
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class RemoveRunGarbage
{

    /**
     * 清除运行垃圾文件
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        if (request()->isAjax() || request()->isPjax() || request()->isPost()) {
            return false;
        }

        // 减少频繁操作,每次请求百分之一几率运行操作
        if (rand(1, 100) !== 1) {
            return false;
        }

        $dir = env('root_path') . 'runtime' . DIRECTORY_SEPARATOR;

        $files = ['cache', 'log', 'temp'];

        $dir_path = [];
        foreach ($files as $key => $value) {
            $dir_path = array_merge($dir_path, (array) glob($dir . $value . DIRECTORY_SEPARATOR . '*'));
        }

        $all_files = [];
        foreach ($dir_path as $key => $path) {
            if (is_file($path)) {
                $all_files[] = $path;
            } elseif (is_dir($path . DIRECTORY_SEPARATOR)) {
                $temp = (array) glob($path . DIRECTORY_SEPARATOR . '*');
                if (!empty($temp)) {
                    $all_files = array_merge($all_files, $temp);
                } else {
                    $all_files[] = $path;
                }
            }
        }

        // 过滤未过期文件与目录
        $days = APP_DEBUG ? strtotime('-1 hour') : strtotime('-7 days');
        foreach ($all_files as $key => $path) {
            if (is_file($path)) {
                if (filectime($path) >= $days) {
                    unset($all_files[$key]);
                }
            } elseif (is_dir($path)) {
                if (filectime($path) >= $days) {
                    unset($all_files[$key]);
                }
            }
        }

        // 为空
        if (empty($all_files)) {
            return false;
        }

        // 随机抽取1000条信息
        shuffle($all_files);
        $all_files = array_slice($all_files, 0, 1000);

        foreach ($all_files as $path) {
            if (is_file($path)) {
                @unlink($path);
            } elseif (is_dir($path)) {
                @rmdir($path);
            }
        }
    }
}
