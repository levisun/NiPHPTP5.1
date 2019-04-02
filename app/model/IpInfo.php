<?php
/**
 *
 * 数据层
 * IP地域信息表
 *
 * @package   NICMS
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;

class IpInfo extends Model
{
    protected $name = 'ipinfo';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $dateFormat = false;
    protected $pk = 'id';
    protected $type = [
        'country_id'  => 'integer',
        'province_id' => 'integer',
        'city_id'     => 'integer',
        'area_id'     => 'integer'
    ];
    protected $field = [
        'id',
        'ip',
        'country_id',
        'province_id',
        'city_id',
        'area_id',
        'isp',
        'update_time',
        'create_time'
    ];
}
