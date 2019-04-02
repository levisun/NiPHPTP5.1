<?php
/**
 *
 * 服务层
 * IP信息类
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\facade\Request;
use app\library\Filter;
use app\model\IpInfo;
use app\model\Region;

class Ip
{

    /**
     * 查询IP地址信息
     * @access public
     * @param  string 请求IP地址
     * @return array
     */
    public static function info(string $_ip = null)
    {
        $_ip = $_ip ? $_ip : Request::ip();

        if (self::validate($_ip) === true) {
            // 查询IP地址库
            $region = self::query($_ip);

            // 存在更新信息
            if (!empty($region) && $region['update_time'] <= strtotime('-7 days')) {
                self::update($_ip);
            }

            // 不存在新建信息
            if (empty($region)) {
                $result = self::added($_ip);
                if ($result !== false) {
                    $region = $result;
                }
            } else {
                unset($region['id'], $region['update_time']);
            }

            return $region;
        } else {
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



        // if (self::validate() === false) {
        //     return [
        //         'ip'          => $_ip,
        //         'country'     => 'ERROR',
        //         'province'    => 'ERROR',
        //         'city'        => 'ERROR',
        //         'area'        => 'ERROR',
        //         'country_id'  => '',
        //         'province_id' => '',
        //         'city_id'     => '',
        //         'area_id'     => '',
        //         'region'      => '',
        //         'isp'         => '',
        //     ];
        // }

    }

    /**
     * 验证IP
     * @access private
     * @static
     * @param  string  $_ip
     * @return array
     */
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
        return
        IpInfo::view('ipinfo i', ['id', 'ip', 'isp', 'update_time'])
        ->view('region country', ['id' => 'country_id', 'name' => 'country'], 'country.id=i.country_id')
        ->view('region region', ['id' => 'region_id', 'name' => 'region'], 'region.id=i.province_id')
        ->view('region city', ['id' => 'city_id', 'name' => 'city'], 'city.id=i.city_id')
        ->view('region area', ['id' => 'area_id', 'name' => 'area'], 'area.id=i.area_id', 'LEFT')
        ->where([
            ['i.ip', '=', $_ip]
        ])
        ->cache(__METHOD__ . $_ip)
        ->find()
        ->toArray();
    }

    /**
     * 查询地址ID
     * @access private
     * @static
     * @param  string  $_name
     * @return int
     */
    private static function queryRegion($_name, $_pid)
    {
        $_name = Filter::default($_name, true);

        $result =
        Region::where([
            ['pid', '=', $_pid],
            ['name', 'LIKE', $_name . '%']
        ])
        ->cache(__METHOD__ . $_name . $_pid, 28800)
        ->value('id');

        return $result ? $result : 0;
    }

    /**
     * 插入IP地址库
     * @access private
     * @static
     * @param
     * @return array|false
     */
    private static function added($_ip)
    {
        $result = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $_ip);

        if (!is_null($result) && $ip = json_decode($result, true)) {
            if (!empty($ip) && $ip['code'] == 0) {
                $country = self::queryRegion($ip['data']['country'], 0);
                $isp     = Filter::default($ip['data']['isp'], true);

                if ($country) {
                    $province = self::queryRegion($ip['data']['region'], $country);
                    $city     = self::queryRegion($ip['data']['city'], $province);
                    if ($ip['data']['area']) {
                        $area = self::queryRegion($ip['data']['area'], $city);
                    } else {
                        $area = 0;
                    }

                    $has =
                    IpInfo::where([
                        ['ip', '=', $_ip]
                    ])
                    ->value('id');

                    if (!$has) {
                        IpInfo::insert([
                            'ip'          => $_ip,
                            'country_id'  => $country,
                            'province_id' => $province,
                            'city_id'     => $city,
                            'area_id'     => $area,
                            'isp'         => $isp,
                            'update_time' => time(),
                            'create_time' => time()
                        ]);
                    }
                }
            } else {
                return [
                    'ip'          => $_ip,
                    'country'     => $ip['data']['country'],
                    'province'    => $ip['data']['region'],
                    'city'        => $ip['data']['city'],
                    'area'        => $ip['data']['area'],
                    'country_id'  => '',
                    'province_id' => '',
                    'city_id'     => '',
                    'area_id'     => '',
                    'isp'         => $isp,
                ];
            }
        } else {
            return false;
        }
    }
}
