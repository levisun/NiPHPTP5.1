<?php
/**
 *
 * 服务层
 * 删除运行垃圾文件
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\App;
use think\facade\Log;
use think\facade\Request;
use app\library\Base64;

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
     * @return bool
     */
    public function remove(): bool
    {
        // 减少频繁操作,每次请求百分之一几率运行操作
        if (rand(1, 100) !== 1) {
            return false;
        }

        $dirOrPath = [];
        $dirOrPath = array_merge($dirOrPath, (array) glob(app()->getRuntimePath() . '*'));
        $dirOrPath = array_merge($dirOrPath, (array) glob(app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . '*'));
        $dirOrPath = $this->getAll($dirOrPath);

        // 为空
        if (!empty($dirOrPath)) {
            // 随机抽取1000条信息
            shuffle($dirOrPath);
            $dirOrPath = array_slice($dirOrPath, 0, 1000);

            foreach ($dirOrPath as $path) {
                if (is_file($path) && stripos($path, '_skl_') === false) {
                    @unlink($path);
                } elseif (is_dir($path)) {
                    @rmdir($path);
                }
            }
            clearstatcache();
            Log::record('[GARBAGE] 删除垃圾信息!', 'alert');
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
        $days = APP_DEBUG ? strtotime('-3 days') : strtotime('-7 days');

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
