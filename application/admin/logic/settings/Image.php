<?php
/**
 *
 * 图片设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Image.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

class Image
{

    /**
     * 查询图片设置数据
     * @access public
     * @param
     * @return array
     */
    public function getImageConfig()
    {
        $map = [
            ['name', 'in', 'auto_image,add_water,water_type,water_location,water_text,water_image,article_module_width,article_module_height,ask_module_width,ask_module_height,download_module_width,download_module_height,job_module_width,job_module_height,link_module_width,link_module_height,page_module_width,page_module_height,picture_module_width,picture_module_height,product_module_width,product_module_height'],
            ['lang', '=', lang(':detect')],
        ];

        // 实例化设置表模型
        $config = model('Config');

        $result =
        $config->field(true)
        ->where($map)
        ->select();

        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = $value['value'];
        }

        return $data;
    }

    /**
     * 保存修改图片设置
     * @access public
     * @param  array  $form_data
     * @return mixed
     */
    public function saveImageConfig($form_data)
    {
        // 实例化设置表模型
        $config = model('Config');

        $sql = [];
        $map = $data = [];
        foreach ($form_data as $key => $value) {
            $map  = [
                ['name', '=', $key],
            ];
            $data = ['value' => $value];

            $config->where($map)
            ->update($data);
        }

        return true;
    }
}