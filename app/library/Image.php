<?php
/**
 *
 * 服务层
 * 缩略图
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\facade\Env;

class Image
{

    private $rootPath;
    private $fontPath;

    /**
     * 构造方法
     * @access public
     * @param  string $_input_name
     * @return void
     */
    public function __construct()
    {
        $this->rootPath = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
        $this->fontPath = $this->rootPath . 'static' . DIRECTORY_SEPARATOR .
                            'font' . DIRECTORY_SEPARATOR . 'simhei.ttf';
    }

    /**
     * 缩略图
     * @param  string  $_imgname 图片名
     * @param  integer $_width   宽
     * @param  integer $_height  高
     * @return string
     */
    public function thumb(string $_imgname, int $_width = 150, int $_height = 150): string
    {
        $img_path = trim($_imgname, '/');
        $img_path = str_replace('/', DIRECTORY_SEPARATOR, $img_path);

        $ext = pathinfo($this->rootPath . $img_path, PATHINFO_EXTENSION);
        $thumb_path = str_replace('.' . $ext, '', $img_path) . $_width . 'x' . $_height . '.' . $ext;

        if (!is_file($this->rootPath . $thumb_path) && is_file($this->rootPath . $img_path)) {
            $image = \think\Image::open($this->rootPath . $img_path);
            if ($image->width() > $_width || $image->height() > $_height) {
                $image->thumb($_width, $_height, \think\Image::THUMB_FILLED);
                $image->text('testing', $this->fontPath, rand(10, 20));
                $image->save($this->rootPath . $thumb_path);
            }
            $_imgname = '/' . str_replace(DIRECTORY_SEPARATOR, '\\', $thumb_path);
        }

        return $_imgname;
    }
}
