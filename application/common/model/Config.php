<?php
/**
 *
 * 配置表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class Config extends Model
{
    protected $name = 'config';
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'name',
        'value',
        'lang'
    ];


}
