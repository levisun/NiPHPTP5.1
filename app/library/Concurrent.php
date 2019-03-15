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
use think\facade\Env;
use think\facade\Log;
use think\facade\Request;
use app\library\Base64;

class Concurrent
{

    private $logPath;
    private $name;
    private $time;

    public function handle($event, App $app): void
    {
        $controller = Request::controller(true);
        if (!in_array($controller, ['abort', 'admin'])) {

            $this->logPath = Env::get('runtime_path') . 'concurrent' . Base64::flag() . DIRECTORY_SEPARATOR;
            if (!is_dir($this->logPath)) {
                chmod(Env::get('runtime_path'), 0777);
                mkdir($this->logPath, 0777, true);
            }

            $this->name = md5(__DIR__ . Request::ip() . date('Ymd')) . '.php';
            $this->time = md5(Request::header('USER-AGENT') . date('YmdHi'));

            $this->record();

            $this->lock();

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
        if (is_file($this->name . '.lock')) {
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
        if (is_file($this->logPath . $this->name)) {
            $data = include $this->logPath . $this->name;

            if (empty($data[$this->time])) {
                $result = false;
            } elseif ($data[$this->time] >= 50) {
                file_put_contents($this->logPath . $this->name . '.lock', date('YmdHis'));
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 记录访问
     * @access private
     * @param
     * @return void
     */
    private function record(): void
    {
        if (is_file($this->logPath . $this->name)) {
            $result = include $this->logPath . $this->name;
        } else {
            $result = [
                $this->time => 1
            ];
        }

        if (is_array($result)) {
            $result[$this->time] = empty($result[$this->time]) ? 1 : $result[$this->time]+1;
            file_put_contents($this->logPath . $this->name, '<?php return ' . var_export($result, true) . ';');
        }

    }
}
