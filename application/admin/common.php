<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application\admin
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: common.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */

function upload_to_javasecipt($_update_file)
{
    if (request()->param('type') == 'ckeditor') {
        # code...
    } elseif (request()->param('type') == 'album') {
        // 相册
        $id = request()->param('id');
        $javascript = '<script type="text/javascript">';
        $javascript .= 'opener.document.getElementById("album-image-' . $id . '").value="' . $base_file . $_update_file['file_name'] . '";';
        $javascript .= 'opener.document.getElementById("album-thumb-' . $id . '").value="' . $base_file . $_update_file['thumb_name'] . '";';
        $javascript .= 'opener.document.getElementById("img-album-' . $id . '").style.display="";';
        $javascript .= 'opener.document.getElementById("img-album-' . $id . '").src="' . $base_file . $_update_file['thumb_name'] . '";';
        $javascript .= 'window.close();';
        $javascript .= '</script>';
    } else {
        // 普通缩略图
        $id = request()->param('id');
        $javascript = '<script type="text/javascript">';
        $javascript .= 'opener.document.getElementById("img-' . $id . '").style.display="";';
        if ($_update_file['thumb_name']) {
            $javascript .= 'opener.document.getElementById("' . $id . '").value="' . $_update_file['save_dir'] . $_update_file['thumb_name'] . '";';
            $javascript .= 'opener.document.getElementById("img-' . $id . '").src="' . $_update_file['domain'] . $_update_file['save_dir'] . $_update_file['thumb_name'] . '";';
        } else {
            $javascript .= 'opener.document.getElementById("' . $id . '").value="' . $_update_file['save_dir'] . $_update_file['file_name'] . '";';
            $javascript .= 'opener.document.getElementById("img-' . $id . '").src="' . $_update_file['domain'] . $_update_file['save_dir'] . $_update_file['file_name'] . '";';
        }

        $javascript .= 'window.close();';
        $javascript .= '</script>';
    }

    return $javascript;
}

/**
 * 加载语言包
 * @param
 * @return void
 */

function LoadLang()
{
    // 允许的语言
    Lang::setAllowLangList(config('lang_list'));

    $lang_path  = Env::get('app_path') . request()->module();
    $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
    $lang_path .= Lang::detect() . DIRECTORY_SEPARATOR;

    // 加载全局语言包
    Lang::load($lang_path . Lang::detect() . '.php');

    // 加载对应语言包
    $lang_name  = strtolower(request()->controller()) . DIRECTORY_SEPARATOR;
    $lang_name .= strtolower(request()->action());
    Lang::load($lang_path . $lang_name . '.php');
}
