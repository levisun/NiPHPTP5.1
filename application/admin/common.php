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

function upload_to_javasecipt($update_file)
{
    if (request()->param('type') == 'ckeditor') {
        # code...
    } elseif (request()->param('type') == 'album') {
        // 相册
        $id = request()->param('id');
        $javascript = '<script type="text/javascript">';
        $javascript .= 'opener.document.getElementById("album-image-' . $id . '").value="' . $base_file . $update_file['file_name'] . '";';
        $javascript .= 'opener.document.getElementById("album-thumb-' . $id . '").value="' . $base_file . $update_file['thumb_name'] . '";';
        $javascript .= 'opener.document.getElementById("img-album-' . $id . '").style.display="";';
        $javascript .= 'opener.document.getElementById("img-album-' . $id . '").src="' . $base_file . $update_file['thumb_name'] . '";';
        $javascript .= 'window.close();';
        $javascript .= '</script>';
    } else {
        // 普通缩略图
        $id = request()->param('id');
        $javascript = '<script type="text/javascript">';
        $javascript .= 'opener.document.getElementById("img-' . $id . '").style.display="";';
        if ($update_file['thumb_name']) {
            $javascript .= 'opener.document.getElementById("' . $id . '").value="' . $update_file['save_dir'] . $update_file['thumb_name'] . '";';
            $javascript .= 'opener.document.getElementById("img-' . $id . '").src="' . $update_file['domain'] . $update_file['save_dir'] . $update_file['thumb_name'] . '";';
        } else {
            $javascript .= 'opener.document.getElementById("' . $id . '").value="' . $update_file['save_dir'] . $update_file['file_name'] . '";';
            $javascript .= 'opener.document.getElementById("img-' . $id . '").src="' . $update_file['domain'] . $update_file['save_dir'] . $update_file['file_name'] . '";';
        }

        $javascript .= 'window.close();';
        $javascript .= '</script>';
    }

    return $javascript;
}
