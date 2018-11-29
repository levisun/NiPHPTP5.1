<?php
/**
 *
 * 清理运行垃圾 - 行为
 * 过期的数据缓存垃圾,模板编译垃圾
 *
 * @package   NiPHPCMS
 * @category  application\common\behavior
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
        // 阻挡Ajax Pjax Post类型请求
        // 阻挡common模块请求
        if (request_block()) {
            return true;
        }

        // 减少频繁操作,每次请求百分之一几率运行操作
        if (rand(1, 100) !== 1) {
            return true;
        }
        // trace('[behavior] RemoveRunGarbage', 'warning');

        $files = [
            'runtime' . DIRECTORY_SEPARATOR . 'cache',
            // 'runtime' . DIRECTORY_SEPARATOR . 'log',
            // 'runtime' . DIRECTORY_SEPARATOR . 'temp',
            'public'  . DIRECTORY_SEPARATOR . 'html',
        ];

        $dir_path = [];
        foreach ($files as $dir) {
            $dir_path = array_merge($dir_path, (array) glob(env('root_path') . $dir . DIRECTORY_SEPARATOR . '*'));
        }

        $all_files = $this->getDir($dir_path);

        // 为空
        if (empty($all_files)) {
            return true;
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

    /**
     * 获得目录中的所有文件与目录
     * @access private
     * @param  string $_dir_path
     * @return array
     */
    private function getDir($_dir_path)
    {
        $days = APP_DEBUG ? strtotime('-1 hour') : strtotime('-1 days');

        $all_files = [];
        foreach ($_dir_path as $key => $path) {
            if (is_file($path)) {
                // 过滤未过期文件
                if (filectime($path) <= $days) {
                    $all_files[] = $path;
                }
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
