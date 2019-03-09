<?php
/**
 *
 * 服务层
 * 访问日志
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\App;
use think\Response;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Log;
use think\facade\Request;

class Concurrent
{

    private $key;
    private $time;

    public function handle($event, App $app): void
    {
        if (Request::controller(true) !== 'abort') {
            $this->key = md5('CIP' . Request::ip());
            $this->time = date('YmdHi');

            $this->record();

            $this->lock();

            $_302 = $this->check();
            if ($this->check()) {
                if (rand(1, 999) === 1) {
                    Log::record('[并发]' . Request::ip(), 'alert');
                    $url = url('error/502');
                    $response = Response::create($url, 'redirect', 302);
                    throw new HttpResponseException($response);
                }
            }
        }
    }

    /**
     * 锁定IP地址
     * @access private
     * @param
     * @return void
     */
    private function lock()
    {
        if (Cache::has($this->key . 'lock')) {
            Log::record('频繁请求锁定IP:' . Request::ip(), 'alert');
            $url = url('error/502');
            $response = Response::create($url, 'redirect', 302);
            throw new HttpResponseException($response);
        }
    }

    /**
     * 校验请求次数
     * @access private
     * @param
     * @return bool
     */
    private function check()
    {
        if (Cache::has($this->key)) {
            $result = Cache::get($this->key);
            if (empty($result[$this->time])) {
                return false;
            } elseif ($result[$this->time] >= 50) {
                Cache::set($this->key . 'lock', '锁定IP地址');
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 记录访问
     * @access private
     * @param
     * @return void
     */
    private function record(): void
    {
        if (!Cache::has($this->key)) {
            $result = [
                $this->time => 1
            ];
        } else {
            $result = Cache::get($this->key);
        }

        if (empty($result[$this->time])) {
            $result[$this->time] = 1;
        } else {
            $result[$this->time] = $result[$this->time]+1;
        }

        Cache::set($this->key, $result, 0);
    }
}
