<?php
/**
 *
 */
class Base
{
    public $cachePath;

    /**
     * 商城分类
     * 基于天猫商城
     * @access public
     * @param  int    $pid
     * @return array
     */
    public function getCat($pid = 0)
    {
        $map = [
            ['parent_id', '=', $pid],
            ['id', '<>', '3'],
            ['type', '<>', '2'],
        ];
        $collect = model('CollectCat');
        $result =
        $this->field(true)
        ->where($map)
        ->order('sort ASC')
        ->select();

        foreach ($result as $key => $value) {
            if ($value['url']) {
                $result[$key]['url'] = urlencode($value['url']);
            }

            if ($pid) {
                $child = $this->getCat($value['category_id']);
                if ($child) {
                    $result[$key]['child'] = $child;
                }
            }
        }

        return $result;
    }

    /**
     * 采集数据
     * @access protected
     * @param  string  $url
     * @param  array   $params  请求参数
     * @param  string  $charset 数据编码
     * @return array
     */
    protected function snoopy($url, $params = array(), $charset = '', $headers = array())
    {
        $snoopy = new Snoopy;
        $agent = array(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
            'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 6 Build/LYZ28E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',

            'Mozilla/5.0 (BB10; Touch) AppleWebKit/537.1+ (KHTML, like Gecko) Version/10.0.0.1337 Mobile Safari/537.1+',
            'Mozilla/5.0 (MeeGo; NokiaN9) AppleWebKit/534.13 (KHTML, like Gecko) NokiaBrowser/8.5.0 Mobile Safari/534.13',
            'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.187 Mobile Safari/534.11+',
            'Mozilla/5.0 (iPad; CPU OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5',
            'Mozilla/5.0 (iPad; CPU OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
            'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
            'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
            'Mozilla/5.0 (Linux; Android 4.1.2; Nexus 7 Build/JZ054K) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Safari/535.19',
            'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19',
        );
        $key = array_rand($agent, 1);
        $snoopy->agent = $agent[$key];
        $snoopy->headers = $headers;

        if (!$result = $this->collCache($url . json_encode($params))) {
            if (!empty($params)) {
                $snoopy->submit($url, $params);
            } else {
                $snoopy->fetch($url);
            }

            $result = $snoopy->results;

            if ($charset != '' && strtoupper($charset) == 'UTF-8') {
                $result = iconv('GB2312', 'UTF-8//IGNORE', $result);
            }

            $this->collCache($url . json_encode($params), $result);
        }

        return $result;
    }

    /**
     * 缓存
     * @access protected
     * @param  string    $name
     * @param  array     $data
     * @param  int       $expire 4个小时
     * @return mixed
     */
    protected function collCache($name, $data = '', $expire = 14400)
    {
        $this->removeCollCache();

        $name = __CLASS__ . $name;
        $file_name = md5($name) . '.php';

        if (is_file($this->cachePath . $file_name)) {
            $result = include($this->cachePath . $file_name);
            if ($result['time'] >= time()) {
                return htmlspecialchars_decode($result['data']);
            }
        }

        if (!empty($data)) {
            // 过虑回车与空格
            $data = preg_replace('/[\s]+/si', ' ', $data);

            if (false !== strpos($data, '400 The plain HTTP request was sent to HTTPS port')) {
                return false;
            }

            $array = array(
                'name' => $name,
                'data' => htmlspecialchars($data),
                'time' => time() + $expire,
                );
            $array = '<?php return ' . var_export($array, true) . '; ?>';

            file_put_contents($this->cachePath . $file_name, $array, true);
        }

        return false;
    }

    /**
     * 消除过期缓存
     * @access protected
     * @param
     * @return boolean
     */
    protected function removeCollCache()
    {
        $files = (array) glob($this->cachePath . '*');
        if (!empty($files)) {
            $rand = array_rand($files, 10);
            $days = strtotime('-7 days');
            foreach ($files as $key => $value) {
                if (in_array($key, $rand) && $value['time'] <= $days) {
                    unlink($this->cachePath . $value['name']);
                }
            }
        }
    }
}
