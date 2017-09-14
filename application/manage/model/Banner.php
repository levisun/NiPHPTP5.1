<?php
/**
 *
 * 幻灯片表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Banner.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class Banner extends Model
{
    protected $name = 'banner';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $field = [
        'id',
        'pid',
        'name',
        'title',
        'width',
        'height',
        'image',
        'url',
        'hits',
        'sort',
        'create_time',
        'update_time',
        'lang'
    ];
}
