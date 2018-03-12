<?php
/**
 *
 */
class Timing
{
    private $dir = './timing/';
    private $hashName = '';
    private $name = '';
    private $flag = '_sleep_time';

    public function __construct()
    {
        # code...
    }

    public function run($_name, $_open, $_sleep_time = 1800, $_time_out = 86400)
    {
        $this->hashName = md5($_name);
        $this->name     = $_name;

        if (!$this->isRunning($_open)) {
            return ;
        }

        if ($this->hasLock()) {
            return ;
        }

        if (!$this->runStatus($_time_out)) {
            return ;
        }

        $this->request($_sleep_time);
    }

    private function isRunning($_open)
    {
        if (!$_open && is_file($this->dir . $this->hashName . '.timing')) {
            unlink($this->dir . $this->hashName . '.timing');
            $this->runLog('end');
        }

        return $_open;
    }

    /**
     * 检查锁定状态
     * @access private
     * @param
     * @return boolean
     */
    private function hasLock()
    {
        if (!is_file($this->dir . $this->hashName . '.timing')) {
            if (!is_dir($this->dir)) {
                mkdir($this->dir, 0777);
                chmod($this->dir, 0777);
            }

            file_put_contents($this->dir . $this->hashName . '.timing', time());
            $this->runLog('start');
            return false;
        } elseif (empty($_GET[$this->flag])) {
            $this->runLog('lock');
            return true;
        } else {
            $this->runLog('run');
            return false;
        }
    }

    /**
     * 检查运行状态
     * @access private
     * @param  int     $_time_out
     * @return boolean
     */
    private function runStatus($_time_out)
    {
        $_run = file_get_contents($this->dir . $this->hashName . '.timing');
        if (time() >= $_run + $_time_out) {
            $this->runLog('end');
            return false;
        }

        return true;
    }

    /**
     * 请求
     * @access private
     * @param  int     $_sleep_time
     * @return boolean
     */
    private function request($_sleep_time)
    {
        $scheme = $this->isSsl() ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parse = parse_url($url);
        if (!empty($parse['query'])) {
            parse_str($parse['query'], $query);
            $query['_name'] = $this->name;
            $query['_sleep_time'] = time();
            $url = $parse['scheme'] . '://' . $parse['host'] . $parse['path'] . '?' . http_build_query($query);
        } else {
            $url = $parse['scheme'] . '://' . $parse['host'] . $parse['path'] . '?_name=' . $this->name . '&_sleep_time=' . time();
        }
        // $this->runLog($url);

        $_sleep_time = empty($_sleep_time) ? 60 : $_sleep_time;
        set_time_limit($_sleep_time + 600);
        sleep($_sleep_time);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 600);           // 设置超时
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);      // 严格校验
        curl_setopt($curl, CURLOPT_HEADER, false);          // 设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   // 要求结果为字符串且输出到屏幕上
        $result = curl_exec($curl);                         // 运行curl

        return true;
    }

    /**
     *
     */
    private function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 运行日志
     * @access private
     * @param  stirng  $_status
     * @return void
     */
    private function runLog($_status)
    {
        $data = '[' . date('Y-m-d H:i:s') . ']';
        if ($_status == 'start') {
            $data .= "运行开始\r\n";
        } elseif ($_status == 'lock') {
            $data .= "运行锁定\r\n";
        } elseif ($_status == 'run') {
            $data .= "运行中\r\n";
        } elseif ($_status == 'end') {
            $data .= "运行结束\r\n";
        } else {
            $data .= $_status . "\r\n";
        }

        file_put_contents($this->dir . date('Ymd') . $this->name . '.tlog', $data, FILE_APPEND);
    }
}
