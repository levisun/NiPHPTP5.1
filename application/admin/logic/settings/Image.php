<?php
/**
 *
 * 图片设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\settings;

use app\admin\logic\Upload;

class Image extends Upload
{

    /**
     * 查询图片设置数据
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'auto_image,add_water,water_type,water_location,water_text,water_image,article_module_width,article_module_height,ask_module_width,ask_module_height,download_module_width,download_module_height,job_module_width,job_module_height,link_module_width,link_module_height,page_module_width,page_module_height,picture_module_width,picture_module_height,product_module_width,product_module_height'],
            ['lang', '=', lang(':detect')],
        ])
        ->select()
        ->toArray();

        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = $value['value'];
        }

        return $data;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'auto_image'             => input('post.auto_image/f'),
            'article_module_width'   => input('post.article_module_width/f'),
            'article_module_height'  => input('post.article_module_height/f'),
            'picture_module_width'   => input('post.picture_module_width/f'),
            'picture_module_height'  => input('post.picture_module_height/f'),
            'download_module_width'  => input('post.download_module_width/f'),
            'download_module_height' => input('post.download_module_height/f'),
            'page_module_width'      => input('post.page_module_width/f'),
            'page_module_height'     => input('post.page_module_height/f'),
            'product_module_width'   => input('post.product_module_width/f'),
            'product_module_height'  => input('post.product_module_height/f'),
            'job_module_width'       => input('post.job_module_width/f'),
            'job_module_height'      => input('post.job_module_height/f'),
            'link_module_width'      => input('post.link_module_width/f'),
            'link_module_height'     => input('post.link_module_height/f'),
            'ask_module_width'       => input('post.ask_module_width/f'),
            'ask_module_height'      => input('post.ask_module_height/f'),
            'add_water'              => input('post.add_water/f'),
            'water_type'             => input('post.water_type/f'),
            'water_location'         => input('post.water_location/f'),
            'water_text'             => input('post.water_text'),
            'water_image'            => input('post.water_image'),
            '__token__'              => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/settings/image', $receive_data);
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $model_config = model('common/config');

        $map = $data = [];
        foreach ($receive_data as $key => $value) {
            $model_config
            ->allowField(true)
            ->where([
                ['name', '=', $key],
            ])
            ->update([
                'value' => $value
            ]);
        }

        $lang = lang('__nav');
        create_action_log($lang['settings']['child']['image'], 'config_editor');

        return true;
    }
}
