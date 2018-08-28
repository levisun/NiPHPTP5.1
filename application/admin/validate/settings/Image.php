<?php
/**
 *
 * 图片设置 - 设置 - 验证器
 *
 * @package   NiPHPCMS
 * @category  application\admin\validate\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\settings;

use think\Validate;

class Image extends Validate
{
    protected $rule = [
        'auto_image'             => ['require', 'number', 'token'],
        'article_module_width'   => ['require', 'number'],
        'article_module_height'  => ['require', 'number'],
        'picture_module_width'   => ['require', 'number'],
        'picture_module_height'  => ['require', 'number'],
        'download_module_width'  => ['require', 'number'],
        'download_module_height' => ['require', 'number'],
        'page_module_width'      => ['require', 'number'],
        'page_module_height'     => ['require', 'number'],
        'product_module_width'   => ['require', 'number'],
        'product_module_height'  => ['require', 'number'],
        'job_module_width'       => ['require', 'number'],
        'job_module_height'      => ['require', 'number'],
        'link_module_width'      => ['require', 'number'],
        'link_module_height'     => ['require', 'number'],
        'ask_module_width'       => ['require', 'number'],
        'ask_module_height'      => ['require', 'number'],
        'add_water'              => ['require', 'number'],
        'water_type'             => ['require', 'number'],
        'water_location'         => ['require', 'number'],
        'water_text'             => ['require'],
        'water_image'            => ['require'],
    ];

    protected $message = [
        'auto_image.require'             => '{%error image auto remove image}',
        'auto_image.number'              => '{%error image auto remove image}',
        'article_module_width.require'   => '{%error image article module}',
        'article_module_width.number'    => '{%error image article module}',
        'article_module_height.require'  => '{%error image article module}',
        'article_module_height.number'   => '{%error image article module}',
        'picture_module_width.require'   => '{%error image picture module}',
        'picture_module_width.number'    => '{%error image picture module}',
        'picture_module_height.require'  => '{%error image picture module}',
        'picture_module_height.number'   => '{%error image picture module}',
        'download_module_width.require'  => '{%error image download module}',
        'download_module_width.number'   => '{%error image download module}',
        'download_module_height.require' => '{%error image download module}',
        'download_module_height.number'  => '{%error image download module}',
        'page_module_width.require'      => '{%error image page module}',
        'page_module_width.number'       => '{%error image page module}',
        'page_module_height.require'     => '{%error image page module}',
        'page_module_height.number'      => '{%error image page module}',
        'product_module_width.require'   => '{%error image product module}',
        'product_module_width.number'    => '{%error image product module}',
        'product_module_height.require'  => '{%error image product module}',
        'product_module_height.number'   => '{%error image product module}',
        'job_module_width.require'       => '{%error image job module}',
        'job_module_width.number'        => '{%error image job module}',
        'job_module_height.require'      => '{%error image job module}',
        'job_module_height.number'       => '{%error image job module}',
        'link_module_width.require'      => '{%error image link module}',
        'link_module_width.number'       => '{%error image link module}',
        'link_module_height.require'     => '{%error image link module}',
        'link_module_height.number'      => '{%error image link module}',
        'ask_module_width.require'       => '{%error image ask module}',
        'ask_module_width.number'        => '{%error image ask module}',
        'ask_module_height.require'      => '{%error image ask module}',
        'ask_module_height.number'       => '{%error image ask module}',
        'add_water.require'              => '{%error image add water}',
        'add_water.number'               => '{%error image add water}',
        'water_type.require'             => '{%error image water type}',
        'water_type.number'              => '{%error image water type}',
        'water_location.require'         => '{%error image water location}',
        'water_location.number'          => '{%error image water location}',
        'water_text.require'             => '{%error image water text}',
        'water_image.require'            => '{%error image water image}',
    ];
}
