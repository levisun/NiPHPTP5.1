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
    {
        $this->autoUpdate();
    }

    public function eachIpInfo()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $request_url = 'http://ip.taobao.com/service/getIpInfo.php?ip=';
        for ($i=255; $i >= 1; $i--) {
            for ($j=255; $j >= 1; $j--) {
                $insert_all = [];
                for ($k=255; $k >= 1; $k--) {
                    for ($l=255; $l >= 1; $l--) {

                        $ip = $i . '.' . $j . '.' . $k . '.' . $l;
                        $insert_all[] = [
                            'ip' => $ip,
                            'update_time' => time(),
                            'create_time' => time(),
                        ];

                        // $request_url .= $ip;
                        // $result = file_get_contents($request_url);
                        // if (!is_null($result)) {
                        //     $ip = json_decode($result, true);
                        //     if (is_array($ip)) {
                        //         $this->added($ip['data']);
                        //     }
                        // }
                    }
                }
                echo count($insert_all);die();
                db('ipinfo')
                ->insertAll($insert_all);
            }
        }
    }

    public function added($_data)
    {
        $result =
        db('ipinfo')
        ->field(true)
        ->where('id', $_data['ip'])
        ->find();
        if (!$result) {
            db('ipinfo')
            ->insert([
                'ip'          => $_data['ip'],
                // 'country'     => $_data['country'],
                // 'region'      => $_data['region'],
                // 'city'        => $_data['city'],
                // 'area'        => $_data['area'],
                // 'county'      => $_data['county'],
                'update_time' => time(),
                'create_time' => time(),
            ]);
        }
    }

    /**
     * 修改IP地址库
     * @return [type] [description]
     */
    private function autoUpdate()
    {
        $result =
        model('common/IpInfo')
        ->where([
            ['ip', '=', '117.22.144.218']
        ])
        ->value('update_time');
        halt($result);
        if ($result <= strtotime('-1 year')) {
            # code...
        }
    }
}
