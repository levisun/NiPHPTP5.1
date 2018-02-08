<?php
/**
 *
 * 上传文件 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Upload.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use think\Image;
use think\facade\Env;

class Upload
{
    // 上传需要参数
    private $uploadParams;
    // 保存路径
    private $savePath;
    // 验证信息
    private $validate;

    private $uploadFileExt;
    private $uploadFileName;

    /**
     * 上传初始化
     * @access private
     * @param  array   $_params
     * @return void
     */
    private function init($_params)
    {
        $this->uploadParams = $this->params($_params['type'], $_params['model']);
        $this->savePath     = Env::get('root_path') . basename(request()->root());
        $this->savePath    .= DIRECTORY_SEPARATOR . 'upload';
        $this->savePath    .= DIRECTORY_SEPARATOR . $this->uploadParams['dir'];
        // $this->savePath    .= DIRECTORY_SEPARATOR;
        $this->validate     = $this->validate();
    }

    /**
     * 单文件上传
     * @access public
     * @param  array  $_params
     * @return array
     */
    public function fileOne($_params)
    {
        // 初始化
        $this->init($_params);

        $upload = $_params['upload'];

        $result =
        $upload->validate($this->validate)
        ->move($this->savePath);

        if (!$result) {
            return $upload->getError();
        }

        // 上传文件后缀
        $this->uploadFileExt  = $result->getExtension();

        // 上传文件保存名
        $this->uploadFileName = $result->getSaveName();

        // 生成水印
        $this->createWater($this->uploadFileName);

        // 生成缩略图文件名
        $upload_thumb_filename = $this->createThumb($this->uploadFileName);

        return [
            // 'domain'     => request()->domain() . substr(request()->baseFile(), 0, -16),
            'domain'     => request()->domain() . request()->root(),
            'save_dir'   => '/upload/' . $this->uploadParams['dir'],
            'file_name'  => $this->uploadFileName,
            'thumb_name' => $upload_thumb_filename,
        ];
    }

    /**
     * 生成缩略图
     * @access private
     * @param  string  $_file_name
     * @return string
     */
    private function createThumb($_file_name)
    {
        $save_name = '';
        if ($this->uploadParams['create_thumb']) {
            // 组合缩略图文件名
            $save_name = str_replace('.' . $this->uploadFileExt, '_thumb.' . $this->uploadFileExt, $_file_name);
            // 生成缩略图
            $image = Image::open($this->savePath . $_file_name);
            $image->thumb($this->uploadParams['thumb_width'], $this->uploadParams['thumb_height'], Image::THUMB_CENTER)
            ->save($this->savePath . $save_name);

            // 生成水印
            $this->createWater($save_name);
        }
        return $save_name;
    }

    /**
     * 生成水印
     * @access private
     * @param  string  $_file_name
     * @return void
     */
    private function createWater($_file_name)
    {
        if ($this->uploadParams['create_water']) {
            $map = [
                ['name', 'in', 'add_water,water_type,water_location,water_text,water_image'],
                ['lang', '=', lang(':detect')],
            ];

            // 获得水印设置
            $result =
            model('common/config')->field(true)
            ->where($map)
            ->select();

            $config_data = [];
            foreach ($result as $key => $value) {
                $value = $value->toArray();
                $config_data[$value['name']] = $value['value'];
            }

            // 不添加水印
            if (!$config_data['add_water']) {
                return false;
            }

            if ($config_data['water_type']) {
                // 图片水印
                $water_image  = Env::get('root_path') . basename(request()->root());
                $water_image .= $config_data['water_image'];
                $image = Image::open($this->savePath . $_file_name);
                $image->water($water_image, $config_data['water_location'], 50);
                $image->save($this->savePath . $_file_name);
            } else {
                // 文字水印
                $font_path  = Env::get('root_path');
                $font_path .= basename(request()->root()) . DIRECTORY_SEPARATOR;
                $font_path .= 'static' . DIRECTORY_SEPARATOR;
                $font_path .= 'layout' . DIRECTORY_SEPARATOR;
                $font_path .=  'font' . DIRECTORY_SEPARATOR . 'HYQingKongTiJ.ttf';

                $image = Image::open($this->savePath . $_file_name);
                $image->text($config_data['water_text'], $font_path, 20, '#ffffff', $config_data['water_location']);
                $image->save($this->savePath . $_file_name);
            }
        }
    }

    /**
     * 上传需要参数
     * @access private
     * @param  string  $_type  上传类型
     * @param  string  $_model 模型
     * @return array
     */
    private function params($_type, $_model)
    {
        // 安上传类型生成上传目录
        if (in_array($_type, ['image', 'ckeditor'])) {
            $dir = 'images/';
        } else {
            $dir = $_type . '/';
        }

        $thumb_width = $thumb_height = 0;
        if ($_type === 'image') {
            $map = [
                ['name', 'in', $_model . '_module_width,' . $_model . '_module_height'],
                ['lang', '=', lang(':detect')],
            ];

            $result =
            model('common/config')->where($map)
            ->column('name, value');

            $thumb_width = $result[$_model . '_module_width'];
            $thumb_height = $result[$_model . '_module_height'];
        } /*elseif (!in_array($_type, ['water', 'ckeditor'])) {
            $thumb_width = $thumb_height = 500;
        } else {

        }*/

        return [
            'dir'          => $dir,
            'create_water' => $_type === 'image' ? true : false,
            'create_thumb' => $thumb_width ? true : false,
            'thumb_width'  => $thumb_width,
            'thumb_height' => $thumb_height,
        ];
    }

    /**
     * 上传文件验证信息
     * @access private
     * @param
     * @return array
     */
    private function validate()
    {
        $map = [
            ['name', 'in', 'upload_file_type,upload_file_max'],
            ['lang', '=', 'niphp'],
        ];

        $result =
        model('common/config')->field(true)
        ->where($map)
        ->select();

        $validate = [];
        foreach ($result as $value) {
            if ($value['name'] == 'upload_file_max') {
                $validate['size'] = $value['value'] * 1024 * 1024;
            } else {
                $validate['ext'] = str_replace('|', ',', $value['value']);
            }
        }

        return $validate;
    }
}
