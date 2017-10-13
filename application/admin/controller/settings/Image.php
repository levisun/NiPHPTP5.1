<?php
/**
 *
 * 图片设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Image.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Image
{

    /**
     * 获得图片设置数据
     * @access public
     * @param
     * @return array
     */
    public function getImageConfig()
    {
        $basic = logic('Image', 'logic\settings');
        return $basic->getImageConfig();
    }

    /**
     * 保存图片基础设置
     * @access public
     * @param
     * @return mixed
     */
    public function saveImageConfig()
    {
        $form_data = [
            'auto_image'             => request()->post('auto_image/f'),
            'article_module_width'   => request()->post('article_module_width/f'),
            'article_module_height'  => request()->post('article_module_height/f'),
            'picture_module_width'   => request()->post('picture_module_width/f'),
            'picture_module_height'  => request()->post('picture_module_height/f'),
            'download_module_width'  => request()->post('download_module_width/f'),
            'download_module_height' => request()->post('download_module_height/f'),
            'page_module_width'      => request()->post('page_module_width/f'),
            'page_module_height'     => request()->post('page_module_height/f'),
            'product_module_width'   => request()->post('product_module_width/f'),
            'product_module_height'  => request()->post('product_module_height/f'),
            'job_module_width'       => request()->post('job_module_width/f'),
            'job_module_height'      => request()->post('job_module_height/f'),
            'link_module_width'      => request()->post('link_module_width/f'),
            'link_module_height'     => request()->post('link_module_height/f'),
            'ask_module_width'       => request()->post('ask_module_width/f'),
            'ask_module_height'      => request()->post('ask_module_height/f'),
            'add_water'              => request()->post('add_water/f'),
            'water_type'             => request()->post('water_type/f'),
            'water_location'         => request()->post('water_location/f'),
            'water_text'             => request()->post('water_text'),
            'water_image'            => request()->post('water_image'),
            '__token__'              => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Image', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Image', 'logic\settings');
        return $basic->saveImageConfig($form_data);
    }
}
