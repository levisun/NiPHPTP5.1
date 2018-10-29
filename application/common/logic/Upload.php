<?php
/**
 *
 * 上传文件 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */
namespace app\common\logic;

use think\Image;

class Upload
{
    private $type;      // 上传类型
    private $inputName; // 上传表单name
    private $savePath;  // 保存路径

    public function __construct()
    {
        $this->type      = input('param.type');
        $this->inputName = input('param.input_name', 'upload');

        $this->savePath  = env('root_path') . 'public';
        $this->savePath .= DIRECTORY_SEPARATOR . 'upload';
        $this->savePath .= DIRECTORY_SEPARATOR . $this->saveSubDir();
    }

    /**
     * 单文件上传
     * @access public
     * @param
     * @return array
     */
    public function fileOne()
    {
        $file = request()->file($this->inputName);

        if ($upload = $file->validate($this->validateConfig())->rule('uniqid')->move($this->savePath)) {
            // 上传文件保存名
            $save_name = $upload->getSaveName();
            $this->addedWater($save_name);

            // 生成缩略图文件名
            $this->ext = $upload->getExtension();
            $thumb_filename = $this->createThumb($save_name);

            return [
                'domain'     => request()->root(true),
                'save_dir'   => '/upload/' . $this->saveSubDir(),
                'file_name'  => $save_name,
                'thumb_name' => $thumb_filename,
            ];
        } else {
            return $upload->getError();
        }
    }

    /**
     * 多文件上传
     * @access public
     * @param
     * @return array
     */
    public function fileAll()
    {
        $file = request()->file($this->inputName);

        halt($file);
    }

    /**
     * 生成缩略图
     * @access private
     * @param  string  $_file_name
     * @return string
     */
    private function createThumb($_file_name)
    {
        $thumb_config = $this->thumbConfig();
        if ($thumb_config['width']) {
            // 组合缩略图文件名
            $save_name = str_replace('.' . $this->ext, '_thumb.' . $this->ext, $_file_name);
            // 生成缩略图
            $image = Image::open($this->savePath . $_file_name);
            $image->thumb(
                $thumb_config['width'],
                $thumb_config['height'],
                Image::THUMB_CENTER
            )
            ->save($this->savePath . $save_name);

            // 生成水印
            $this->addedWater($save_name);

            return $save_name;
        } else {
            return '';
        }
    }

    /**
     * 添加水印
     * @access private
     * @param  string  $_file_name
     * @return string
     */
    private function addedWater($_file_name)
    {
        $water_config = $this->waterConfig();
        if ($water_config['add_water']) {
            if ($water_config['water_type']) {
                // 图片水印
                $water_image  = env('root_path') . basename(request()->root());
                $water_image .= $water_config['water_image'];

                $image = Image::open($this->savePath . $_file_name);
                $image->water($water_image, $water_config['water_location'], 50);
                $image->save($this->savePath . $_file_name);
            } else {
                // 文字水印
                $font_path  = env('root_path');
                $font_path .= basename(request()->root()) . DIRECTORY_SEPARATOR;
                $font_path .= 'static' . DIRECTORY_SEPARATOR;
                $font_path .= 'layout' . DIRECTORY_SEPARATOR;
                $font_path .=  'font' . DIRECTORY_SEPARATOR . 'HYQingKongTiJ.ttf';

                $image = Image::open($this->savePath . $_file_name);
                $image->text($water_config['water_text'], $font_path, 20, '#ffffff', $water_config['water_location']);
                $image->save($this->savePath . $_file_name);
            }
        }
    }

    /**
     * 获得缩略图配置
     * @access private
     * @param
     * @return array
     */
    private function saveSubDir()
    {
        // 按年,月生成保存目录,适用于多图片
        $dir = date('Ym') . '/';

        // 安上传类型生成上传目录
        if (in_array($this->type, ['image', 'images', 'ckeditor'])) {
            $dir = 'images/' . $dir;
        } else {
            $dir = $this->type . '/' . $dir;
        }

        return $dir;
    }

    /**
     * 上传文件验证信息
     * @access private
     * @param
     * @return array
     */
    private function validateConfig()
    {
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'upload_file_type,upload_file_max'],
            ['lang', '=', 'niphp'],
        ])
        ->select()
        ->toArray();

        $validate = [];
        foreach ($result as $value) {
            if ($value['name'] == 'upload_file_max') {
                $validate['size'] = $value['value'] * 1024 * 1024;
            } else {
                $validate['ext'] = str_replace('，', ',', $value['value']);
            }
        }

        return $validate;
    }

    /**
     * 获得缩略图配置
     * @access private
     * @param
     * @return array
     */
    private function thumbConfig()
    {
        // 按模型查询缩略图配置
        if (in_array($this->type, ['article', 'ask', 'download', 'job', 'link', 'page', 'picture', 'product'])) {
            $result =
            model('common/config')
            ->where([
                ['name', 'in', $this->type . '_module_width,' . $this->type . '_module_height'],
                ['lang', '=', lang(':detect')],
            ])
            ->column('name, value');

            if (!empty($result)) {
                $width  = $result[$this->type . '_module_width'];
                $height = $result[$this->type . '_module_height'];
            }
        }
        // 会员头像
        elseif ($this->type == 'portrait') {
            $width = $height = 300;
        } else {
            $width = $height = false;
        }

        return [
            'width'  => $width,
            'height' => $height,
        ];
    }

    /**
     * 获得水印配置
     * @access private
     * @param
     * @return array
     */
    private function waterConfig()
    {
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'add_water,water_type,water_location,water_text,water_image'],
            ['lang', '=', lang(':detect')],
        ])
        ->select()
        ->toArray();

        $weter_config = [];
        foreach ($result as $key => $value) {
            $weter_config[$value['name']] = $value['value'];
        }

        return $weter_config;
    }
}
