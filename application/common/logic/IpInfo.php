<?php
/**
 *
 * IP归属地 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\logic;

class IpInfo
{

    public function __construct()
    {}

    /**
     * 查询IP地址信息
     * @access public
     * @param  string 请求IP地址
     * @return array
     */
    public function getInfo($_request_ip = false)
    {
        $_request_ip = $_request_ip ? $_request_ip : request()->ip();

        $result = $this->validate($_request_ip);
        if (is_null($result)) {
            return [
                'ip'          => $_request_ip,
                'country'     => '错误IP',
                'province'    => '错误IP',
                'city'        => '错误IP',
                'area'        => '错误IP',
                'country_id'  => '',
                'province_id' => '',
                'city_id'     => '',
                'area_id'     => '',
                'region'      => '',
            ];
        } elseif (!$result) {
            return [
                'ip'          => $_request_ip,
                'country'     => '保留地址或本地局域网',
                'province'    => '内网IP',
                'city'        => '内网IP',
                'area'        => '内网IP',
                'country_id'  => '',
                'province_id' => '',
                'city_id'     => '',
                'area_id'     => '',
                'region'      => '',
            ];
        }

        $result =
        model('common/model/IpInfo')
        ->view('ipinfo i', ['id', 'ip', 'isp', 'update_time'])
        ->view('region country', ['id' => 'country_id', 'name' => 'country'], 'country.id=i.country_id', 'LEFT')
        ->view('region region', ['id' => 'region_id', 'name' => 'region'], 'region.id=i.province_id', 'LEFT')
        ->view('region city', ['id' => 'city_id', 'name' => 'city'], 'city.id=i.city_id', 'LEFT')
        ->view('region area', ['id' => 'area_id', 'name' => 'area'], 'area.id=i.area_id', 'LEFT')
        ->where([
            ['i.ip', '=', $_request_ip]
        ])
        ->cache(__METHOD__ . $_request_ip, 28800)
        ->find();

        $result = $result ? $result->toArray() : [];

        // 存在更新信息
        if (!empty($result) && $result['update_time'] <= strtotime('-30 days')) {
            $this->update($request_ip);
        }

        // 不存在新建信息
        if (empty($result)) {
            $result = $this->added($_request_ip);
            if ($result !== false) {
                $result['region'] = empty($result['region']) ? '' : $result['region'];
                $result['city']   = empty($result['city']) ? '' : $result['city'];
                $result['area']   = empty($result['area']) ? '' : $result['area'];
            }
        } else {
            unset($result['id'], $result['update_time']);
        }

        if (in_array($result['ip'], ['::1', '127.0.0.1'])) {
            $result['country'] = '保留地址或本地局域网';
        }

        // $result['total'] =
        // model('common/model/IpInfo')
        // ->cache(__METHOD__ . 'total', 28800)
        // ->count();

        return $result;
    }

    /**
     * 验证IP
     * @access private
     * @param  string  $_request_ip
     * @return array
     */
    private function validate($_request_ip)
    {
        if (empty($_request_ip)) {
            return null;
        }

        $ip = explode('.', $_request_ip);
        if (count($ip) == 4) {
            foreach ($ip as $key => $value) {
                if ($value != '') {
                    $ip[$key] = (int) $value;
                } else {
                    return null;
                }
            }

            // 保留IP地址段
            // a类 10.0.0.0~10.255.255.255
            // b类 172.16.0.0~172.31.255.255
            // c类 192.168.0.0~192.168.255.255
            if ($ip[0] == 0 || $ip[0] == 10 || $ip[0] == 255) {
                return false;
            } elseif ($ip[0] == 172 && $ip[1] >= 16 && $ip[1] <= 31) {
                return false;
            } elseif ($ip[0] == 172 && $ip[1] = 0) {
                return false;
            } elseif ($ip[0] == 192 && $ip[1] == 168) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }


    /**
     * 插入IP地址库
     * @access private
     * @param
     * @return void
     */
    private function added($_request_ip)
    {
        $result = $this->curl('http://ip.taobao.com/service/getIpInfo.php?ip=' . $_request_ip);
        if (!is_null($result) && $ip = json_decode($result, true)) {
            $country  = $this->queryRegion($ip['data']['country']);
            $province = $this->queryRegion($ip['data']['region']);
            $city     = $this->queryRegion($ip['data']['city']);
            $isp      = safe_filter_strict($ip['data']['isp']);
            if ($ip['data']['area']) {
                $area = $this->queryRegion($ip['data']['area']);
            } else {
                $area = 0;
            }

            if ($country) {
                $has =
                model('common/model/IpInfo')
                ->where([
                    ['ip', '=', $_request_ip]
                ])
                ->value('id');

                if (!$has) {
                    model('common/model/IpInfo')
                    ->allowField(true)
                    ->create([
                        'ip'          => $_request_ip,
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

            return [
                'ip'          => $_request_ip,
                'country'     => $ip['data']['country'],
                'province'    => $ip['data']['region'],
                'city'        => $ip['data']['city'],
                'area'        => $ip['data']['area'],
                'country_id'  => $country,
                'province_id' => $province,
                'city_id'     => $city,
                'area_id'     => $area,
                'isp'         => $isp,
            ];
        } else {
            return false;
        }
    }

    /**
     * 更新IP地址库
     * @access private
     * @param
     * @return void
     */
    private function update($_request_ip)
    {
        $result = $this->curl('http://ip.taobao.com/service/getIpInfo.php?ip=' . $_request_ip);
        if (!is_null($result) && $ip = json_decode($result, true)) {
            $country  = $this->queryRegion($ip['data']['country']);
            $province = $this->queryRegion($ip['data']['region']);
            $city     = $this->queryRegion($ip['data']['city']);
            $isp      = safe_filter_strict($ip['data']['isp']);
            if ($ip['data']['area']) {
                $area = $this->queryRegion($ip['data']['area']);
            } else {
                $area = 0;
            }

            model('common/model/IpInfo')
            ->allowField(true)
            ->where([
                ['ip', '=', $_request_ip],
            ])
            ->update([
                'country_id'  => $country,
                'province_id' => $province,
                'city_id'     => $city,
                'area_id'     => $area,
                'isp'         => $isp,
                'update_time' => time()
            ]);
        }
    }

    /**
     * 查询地址ID
     * @access private
     * @param  string  $_name
     * @return int
     */
    private function queryRegion($_name)
    {
        $_name = safe_filter_strict($_name);

        $result =
        model('common/model/region')
        ->where([
            ['name', 'LIKE', $_name . '%']
        ])
        ->cache(__METHOD__ . $_name, 28800)
        ->value('id');

        return $result ? $result : 0;
    }

    /**
     * url
     * @access private
     * @param  string  $_url url
     * @return mixed
     */
    private function curl($_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $_url);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);

        $headers = ['content-type: application/x-www-form-urlencoded;charset=UTF-8'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $result = curl_exec($curl);

        if ($result) {
            curl_close($curl);
            return $result;
        } else {
            $error = curl_errno($curl);
            curl_close($curl);
            return 'curl出错,错误码:' . $error;
        }
    }
}
