<?php
/**
 *
 * 错误日志 - 扩展 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\expand
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
        $dir  = env('runtime_path') . 'log' . DIRECTORY_SEPARATOR . '*';
        $file = (array) glob($dir);
        rsort($file);

        $file_dir = [];
        foreach ($file as $key => $value) {
            $temp = (array) glob($value . DIRECTORY_SEPARATOR . '*');
            rsort($temp);

            foreach ($temp as $path) {
                $date = substr($value, -6);
                $name = basename($path);
                if (false !== strpos($name, '_error')) {
                    $file_dir[] = [
                        'name' => $date . $name,
                        'time' => filectime($path),
                        'size' => file_size($path),
                        'show' => url('expand/elog', ['operate' => 'show', 'id' => encrypt($date . DIRECTORY_SEPARATOR . $name)]),
                    ];
                }
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

        $path  = env('runtime_path') . 'log' . DIRECTORY_SEPARATOR . $name;
        $result = '';
        if (is_file($path)) {
            $result = file_get_contents($path);
            $pattern = [
                env('root_path')     => '',

                'application\common' => '公众',
                'application\admin'  => '后台',
                'application\cms'    => '前台',
                'application\mall'   => '商城',
                'application\member' => '会员',
                'application\wecaht' => '微信',

                'app\common'         => '公众',
                'app\admin'          => '后台',
                'app\cms'            => '前台',
                'app\mall'           => '商城',
                'app\member'         => '会员',
                'app\wecaht'         => '微信',
                'app'                => '',
            ];
            $result = str_replace(array_keys($pattern), array_values($pattern), $result);
        }

        return $result;
    }
}
