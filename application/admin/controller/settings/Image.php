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
        return $basic->getBasicConfig();
    }

    /**
     * 保存图片基础设置
     * @access public
     * @param  array  $form_data
     * @return mixed
     */
    public function saveImageConfig($form_data)
    {
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
