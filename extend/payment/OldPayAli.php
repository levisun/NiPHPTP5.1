<?php
/**
 *
 */
class OldPayAli
{
    protected $gatewayUrl = 'https://mapi.alipay.com/gateway.do?';

    // 消息验证地址
    protected $httpsVerifyUrl = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    protected $httpVerifyUrl  = 'http://notify.alipay.com/trade/notify_query.do?';

    // 支付配置
    protected $config = array();

    protected $params = array();

    function __construct($_config)
    {
        $this->config['service']        = 'create_direct_pay_by_user';

        // 编码
        $this->config['_input_charset'] = 'utf-8';
        // 签名类型
        $this->config['sign_type']      = 'MD5';
        // 合作身份者id，以2088开头的16位纯数字
        $this->config['partner']        = $_config['partner'];
        // 收款支付宝账号，一般情况下收款账号就是签约账号
        $this->config['seller_email']   = $_config['seller_email'];
        // 安全检验码，以数字和字母组成的32位字符
        $this->config['key']            = $_config['key'];
        // CA证书路径地址，用于curl中ssl校验
        $this->config['cacert']         = !empty($_config['cacert']) ? $_config['cacert'] : '';
        $this->config['transport']      = !empty($_config['transport']) ? $_config['transport'] : 'http';
    }

    /**
     * PC端支付
     * @access public
     * @param  array $_params
     * @return array
     */
    public function pcPay($_params)
    {
        $this->params = array(
            'partner'           => $this->config['partner'],
            'seller_email'      => $this->config['seller_email'],
            '_input_charset'    => $this->config['_input_charset'],

            'payment_type'      => !empty($_params['payment_type']) ? $_params['payment_type'] : '1',
            'service'           => 'create_direct_pay_by_user',

            // 异步回调地址
            'notify_url'        => $_params['notify_url'],
            // 同步回调地址
            'return_url'        => $_params['return_url'],
            // 订单号
            'out_trade_no'      => $_params['out_trade_no'],
            // 订单名称
            'subject'           => $_params['subject'],
            // 支付金额
            'total_fee'         => $_params['total_fee'],
            // 订单描述
            'body'              => $_params['body'],
            // 商品展示地址
            'show_url'          => !empty($_params['show_url']) ? $_params['show_url'] : '',
            // 防钓鱼时间戳
            'anti_phishing_key' => !empty($_params['anti_phishing_key']) ? $_params['anti_phishing_key'] : '',
        );

        $this->params['sign']      = $this->getSign($this->params);
        $this->params['sign_type'] = $this->config['sign_type'];
        // $this->params['exter_invoke_ip'] = $this->ip();

        $js = '';
        foreach ($this->params as $key => $value) {
            if ($value !== '') {
                $js .= $key . '=' . urlencode($value) . '&';
            }
        }

        $result = $this->params;
        $result['action'] = $this->gatewayUrl;
        $result['pay_url'] = $this->gatewayUrl . trim($js, '&');
        return $result;
    }

    /**
     * 移动端支付
     * @access public
     * @param  array $_params
     * @return array
     */
    public function wapPay($_params)
    {
        $this->params = array(
            'partner'           => $this->config['partner'],
            'seller_email'      => $this->config['seller_email'],
            '_input_charset'    => $this->config['_input_charset'],

            'payment_type'      => !empty($_params['payment_type']) ? $_params['payment_type'] : '1',
            'service'           => 'alipay.wap.create.direct.pay.by.user',

            // 异步回调地址
            'notify_url'        => $_params['notify_url'],
            // 同步回调地址
            'return_url'        => $_params['return_url'],
            // 订单号
            'out_trade_no'      => $_params['out_trade_no'],
            // 订单名称
            'subject'           => $_params['subject'],
            // 支付金额
            'total_fee'         => $_params['total_fee'],
            // 订单描述
            'body'              => $_params['body'],
            // 商品展示地址
            'show_url'          => !empty($_params['show_url']) ? $_params['show_url'] : '',
            // 防钓鱼时间戳
            'anti_phishing_key' => !empty($_params['anti_phishing_key']) ? $_params['anti_phishing_key'] : '',
        );

        $this->params['sign']      = $this->getSign($this->params);
        $this->params['sign_type'] = $this->config['sign_type'];

        $url = '';
        foreach ($this->params as $key => $value) {
            if ($value !== '') {
                $url .= $key . '=' . urlencode($value) . '&';
            }
        }

        $result = $this->params;
        $result['action'] = $this->gatewayUrl;
        $result['pay_url'] = $this->gatewayUrl . trim($url, '&');
        return $result;
    }

    /**
     * 异步回调
     * @access public
     * @return boolean|array
     */
    public function notify()
    {
        if (empty($_POST)) {
            return false;
        }

        $sign = $this->getSign($_POST);

        $transport = strtolower($this->config['transport']);

        if ($transport == 'https') {
            $veryfy_url = $this->httpsVerifyUrl;
        } else {
            $veryfy_url = $this->httpVerifyUrl;
        }

        $veryfy_url .= 'partner=' . $this->config['partner'] . '&notify_id=' . $_POST["notify_id"];

        $result = $this->getCurl($veryfy_url, $this->config['cacert']);

        if ($sign == $_POST['sign'] && $result === 'true') {
            return array(
                'out_trade_no' => $_POST['out_trade_no'],    // 商户订单号
                'total_fee'    => $_POST['total_fee'],       // 支付金额
                'trade_no'     => $_POST['trade_no'],        // 支付宝订单号
            );
        } else {
            return false;
        }
    }

    /**
     * 同步回调
     * @access public
     * @return boolean|array
     */
    public function respond()
    {
        if (empty($_GET)) {
            return false;
        }

        $sign = $this->getSign($_GET);

        $transport = strtolower($this->config['transport']);

        if ($transport == 'https') {
            $veryfy_url = $this->httpsVerifyUrl;
        } else {
            $veryfy_url = $this->httpVerifyUrl;
        }

        $veryfy_url .= 'partner=' . $this->config['partner'] . '&notify_id=' . $_GET["notify_id"];

        $result = $this->getCurl($veryfy_url, $this->config['cacert']);

        if ($sign == $_GET['sign'] && $result === 'true') {
            return array(
                'out_trade_no' => $_GET['out_trade_no'],    // 商户订单号
                'total_fee'    => $_GET['total_fee'],       // 支付金额
                'trade_no'     => $_GET['trade_no'],        // 支付宝订单号
            );
        } else {
            return false;
        }
    }

    private function getCurl($_url, $_cacert_url)
    {
        $curl = curl_init($_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);              // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);      // 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);   // SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);      // 严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $_cacert_url);   // 证书地址
        $result = curl_exec($curl);

        if ($result) {
            curl_close($curl);
            return $result;
        } else {
            $error = curl_errno($curl);
            curl_close($curl);
            return 'curl出错，错误码:' . $error;
        }
    }

    /**
     * 签名
     * @access private
     * @param  array   $_params
     * @return mixed
     */
    private function getSign($_params)
    {
        ksort($_params);
        reset($_params);

        $to_be_signed = '';

        foreach ($_params as $key => $value) {
            if (!in_array($key, array('sign', 'sign_type')) && $value !== '') {
                $to_be_signed .= $key . '=' . $value . '&';
            }
        }

        $to_be_signed = trim($to_be_signed, '&');

        return md5($to_be_signed . $this->config['key']);
    }

    /**
     * 生成订单号
     * @access private
     * @param  string $other
     * @return string
     */
    private function orderNo($other = '')
    {
        list($micro, $time) = explode(' ', microtime());
        $micro = str_pad($micro * 1000000, 6, 0, STR_PAD_LEFT);

        return $time . $micro . mt_rand(111, 999) . $other;
    }

    /**
     * 获取客户端IP地址
     * @access private
     * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    private function ip($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}
