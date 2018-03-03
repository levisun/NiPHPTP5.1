<?php
/**
 *
 * 错误日志 - 扩展 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\expand
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\expand;

class Elog
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $dir  = env('root_path') . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . '*';
        $file = (array) glob($dir);
        rsort($file);

        $file_dir = [];
        foreach ($file as $key => $value) {
            $temp = (array) glob($value . DIRECTORY_SEPARATOR . '*');
            rsort($temp);

            foreach ($temp as $path) {
                $size = filesize($path);

                $a   = ['B', 'KB', 'MB', 'GB', 'TB'];
                $pos = 0;
                while ($size >= 1024) {
                    $size /= 1024;
                    $pos++;
                }

                $date = substr($value, -6);
                $name = basename($path);
                $file_dir[$date . $name] = [
                    // 'path' => $path,
                    'time' => filectime($path),
                    'size' => round($size, 2) . ' ' . $a[$pos],
                    'show' => url('expand/elog', ['operate' => 'show', 'id' => encrypt($date . DIRECTORY_SEPARATOR . $name)]),
                ];
            }
        }

        return $file_dir;
    }

    /**
     * 查看日志内容
     * @access public
     * @param
     * @return string
     */
    public function find()
    {
        $name = input('post.id');
        $name = decrypt($name);

        $path  = env('root_path') . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . $name;
        $result = '';
        if (is_file($path)) {
            $result = file_get_contents($path);
        }

        return $result;
    }
}
