<?php
/**
 * 文件操作类
 *
 * @package   NiPHPCMS
 * @category  extend\util\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: File.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/01/03
 */
namespace util;

class File
{

    /**
     * 获得目录下所有文件
     * @static
     * @access public
     * @param  string $_dir
     * @return array
     */
    public static function all($_dir)
    {
        if (substr($_dir, -1) !== DIRECTORY_SEPARATOR) {
            $_dir .= DIRECTORY_SEPARATOR;
        }

        $files = [];
        if (is_dir($_dir)) {
            $files = (array) glob($_dir . '*');
        }

        return $files;
    }

    /**
     * 删除文件或目录
     * @static
     * @access public
     * @param  string $_dir_file
     * @return void
     */
    public static function remove($_dir_file)
    {
        if (is_dir($_dir_file)) {
            $result = self::all($_dir_file);
            foreach ($result as $path) {
                if (is_dir($path)) {
                    self::remove($path);
                    rmdir($path);
                } else {
                    unlink($path);
                }
            }
        } elseif (is_file($_dir_file)) {
            unlink($_dir_file);
        }
    }
}
