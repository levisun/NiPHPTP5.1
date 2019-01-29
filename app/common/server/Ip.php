<?php
/**
 *
 * IP信息类 - 方法库
 *
 * @package   NiPHP
 * @category  app\common\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\library;

// use think\App;
// use think\facade\Env;
use think\facade\Request;

use app\common\model\IpInfo;

class Ip
{


    public static function info(string $_ip = null)
    {
        $_ip = $_ip ? $_ip : Request::ip();

        if (self::validate() === false) {
            return [
                'ip'          => $_ip,
                'country'     => 'ERROR',
                'province'    => 'ERROR',
                'city'        => 'ERROR',
                'area'        => 'ERROR',
                'country_id'  => '',
                'province_id' => '',
                'city_id'     => '',
                'area_id'     => '',
                'region'      => '',
                'isp'         => '',
            ];
        }

    }

    private static function validate(string $_ip): bool
    {
        $_ip = explode('.', $_ip);
        if (count($_ip) == 4) {
            foreach ($_ip as $key => $value) {
                if ($value != '') {
                    $_ip[$key] = (int) $value;
                } else {
                    return false;
                }
            }

            // 保留IP地址段
            // a类 10.0.0.0~10.255.255.255
            // b类 172.16.0.0~172.31.255.255
            // c类 192.168.0.0~192.168.255.255
            if ($_ip[0] == 0 || $_ip[0] == 10 || $_ip[0] == 255) {
                return false;
            } elseif ($_ip[0] == 172 && $_ip[1] >= 16 && $_ip[1] <= 31) {
                return false;
            } elseif ($_ip[0] == 127 && $_ip[1] == 0) {
                return false;
            } elseif ($_ip[0] == 192 && $_ip[1] == 168) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 查询IP地址库
     * @access private
     * @static
     * @param
     * @return array
     */
    private static function query($_ip)
    {
        $result =
        IpInfo::view('ipinfo i', ['id', 'ip', 'isp', 'update_time'])
        ->view('region country', ['id' => 'country_id', 'name' => 'country'], 'country.id=i.country_id')
        ->view('region region', ['id' => 'region_id', 'name' => 'region'], 'region.id=i.province_id')
        ->view('region city', ['id' => 'city_id', 'name' => 'city'], 'city.id=i.city_id')
        ->view('region area', ['id' => 'area_id', 'name' => 'area'], 'area.id=i.area_id', 'LEFT')
        ->where([
            ['i.ip', '=', $_ip]
        ])
        ->cache(__METHOD__ . $_ip)
        ->find();

        return $result ? $result->toArray() : [];
    }
}
