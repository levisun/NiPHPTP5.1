<?php
/**
 *
 * IP归属地 - 业务层
 * 基于角色的数据库方式验证类
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
     * @param
     * @return array
     */
    public function getInfo()
    {
        $request_ip = input('param.ip', request()->ip());

        $result =
        model('common/IpInfo')
        ->view('ipinfo i', ['id', 'ip', 'update_time'])
        ->view('region country', ['name' => 'country'], 'country.id=i.country_id', 'LEFT')
        ->view('region region', ['name' => 'region'], 'region.id=i.province_id', 'LEFT')
        ->view('region city', ['name' => 'city'], 'city.id=i.city_id', 'LEFT')
        ->view('region area', ['name' => 'area'], 'area.id=i.area_id', 'LEFT')
        ->where([
            ['i.ip', '=', $request_ip]
        ])
        ->cache('IPINFO GETINFO' . $request_ip)
        ->find();

        $result = $result ? $result->toArray() : [];

        // 存在更新信息
        if ($result && $result['update_time'] <= strtotime('-1 year')) {
            $this->update($request_ip);
        }

        // 不存在新建信息
        if (!$result) {
            $result = $this->added($request_ip);
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

        return $result;
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
            if ($ip['data']['area']) {
                $area = $this->queryRegion($ip['data']['area']);
            } else {
                $area = 0;
            }

            if ($country) {
                model('common/IpInfo')
                ->allowField(true)
                ->create([
                    'ip'          => $_request_ip,
                    'country_id'  => $country,
                    'province_id' => $province,
                    'city_id'     => $city,
                    'area_id'     => $area,
                    'update_time' => time(),
                    'create_time' => time()
                ]);
            }

            return [
                'ip'          => $_request_ip,
                'country_id'  => $ip['data']['country'],
                'province_id' => $ip['data']['region'],
                'city_id'     => $ip['data']['city'],
                'area_id'     => $ip['data']['area'],
                // 'update_time' => time(),
                // 'create_time' => time()
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
            if ($ip['data']['area']) {
                $area = $this->queryRegion($ip['data']['area']);
            } else {
                $area = 0;
            }

            model('common/IpInfo')
            ->allowField(true)
            ->where([
                ['ip', '=', $_request_ip],
            ])
            ->update([
                'country_id'  => safe_filter($country, true, true),
                'province_id' => safe_filter($province, true, true),
                'city_id'     => safe_filter($city, true, true),
                'area_id'     => safe_filter($area, true, true),
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
        $_name = safe_filter($_name, true, true);

        $result =
        model('common/region')
        ->where([
            ['name', 'LIKE', $_name . '%']
        ])
        ->cache('IPINFO QUERYREGION' . $_name)
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

        $headers = ['content-type: application/x-www-form-urlencoded;charset=UTF-8'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
