<?php
/**
 *
 * 服务层
 * 上传类
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

use think\Image;
// use think\App;
// use think\Response;
// use think\exception\HttpException;
// use think\exception\HttpResponseException;
// use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
// use think\facade\Lang;
// use think\facade\Log;
use think\facade\Request;
// use app\library\Accesslog;
// use app\library\Base64;
// use app\library\Filter;
// use app\library\Siteinfo;

class Upload
{
    private $rule = [
        'size' => 200*1024,
        'ext'  => ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'zip', 'rar', 'doc', 'ppt']
    ];

    private $savePath;
    private $subDir;

    /**
     * 构造方法
     * @access public
     * @param  string $_input_name
     * @return void
     */
    public function __construct()
    {
        $this->subDir = date('Ym');
        $this->savePath = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                            'uploads' . DIRECTORY_SEPARATOR .
                            $this->subDir . DIRECTORY_SEPARATOR;
    }

    /**
     * 保存文件
     * @access public
     * @param  string $_input_name 表单名
     * @return array  文件信息
     */
    public function save(string $_input_name = 'upload'): array
    {
        $file = Request::file($_input_name);

        $result = [];

        // 多文件上传
        if (is_array($file)) {
            foreach ($file as $key => $object) {
                $result[] = $this->saveFile($object);
            }
        }

        // 单文件上传
        elseif (is_object($file)) {
            $result = $this->saveFile($file);
        }

        //
        else {

        }


        return $result;
    }

    /**
     * 保存文件
     * @param  object $_object
     * @param  string $_type
     * @return string
     */
    private function saveFile(object $_object)
    {
        $_object->validate($this->rule);
        $_object->rule('uniqid');
        if ($result = $_object->move($this->savePath)) {
            // 图片文件 压缩图片
            if (in_array($result->getExtension(), ['gif', 'jpg', 'jpeg', 'bmp', 'png'])) {
                $save_name = $result->getSaveName();
                $image = Image::open($this->savePath . $save_name);
                // 图片大于800像素 统一缩放到800像素
                $width = Request::param('width/f', 800);
                $height = Request::param('height/f', 800);
                if ($image->width() > $width || $image->height() > $height) {
                    $image->thumb($width, $height, Image::THUMB_SCALING);
                }
                $image->save($this->savePath . $save_name, null, 60);
            }

            return [
                'ext'      => $result->getExtension(),
                'name'     => $result->getSaveName(),
                'original' => $result->getBaseName('.' . $result->getExtension()),
                'size'     => $result->getSize(),
                'url'      => '/uploads/' . $this->subDir . '/' .  $result->getSaveName(),
            ];
        } else {
            return [
                'error' => $_object->getError(),
            ];
        }
    }
}
