<?php
/**
 *
 */


/*
$param = array(
    'body'         => '商品描述 128位',
    'subject'      => '商品详情 256位',,
    'out_trade_no' => '商户订单号 64位 数字',
    'total_amount' => '1.00 单位元',
    'product_code' => 'QUICK_WAP_WAY',
    'notify_url'   => '异步通知回调地址,不能携带参数',
    'return_url'   => '同步通知回调地址,不能携带参数',
);
*/
namespace payment;

class PayAli
{
    protected $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    // 支付配置
    protected $config = [];

    protected $params = [];

    function __construct($_config)
    {
        $this->config = [
            'app_id'        => $_config['app_id'],
            'format'        => !empty($_config['format']) ? $_config['format'] : 'JSON',
            'charset'       => !empty($_config['charset']) ? $_config['charset'] : 'UTF-8',
            'sign_type'     => !empty($_config['sign_type']) ? $_config['sign_type'] : 'RSA2',
            'version'       => !empty($_config['version']) ? $_config['version'] : '1.0',
            'rsa_file_path' => $_config['rsa_file_path'],
        ];
    }

    public function wapPay($_params)
    {
        $_params['product_code'] = 'QUICK_WAP_WAY';

        $return_url = $_params['return_url'];
        $notify_url = $_params['notify_url'];
        unset($_params['return_url'], $_params['notify_url']);

        $this->params = $_params;
        $this->params['biz_content']  = json_encode($_params);

        $this->params['method']       = 'alipay.trade.wap.pay';
        $this->params['timestamp']    = date('Y-m-d H:i:s');
        $this->params['app_id']       = $this->config['app_id'];
        $this->params['format']       = $this->config['format'];
        $this->params['charset']      = $this->config['charset'];
        $this->params['sign_type']    = $this->config['sign_type'];
        $this->params['version']      = $this->config['version'];

        $this->params['return_url']   = $this->config['return_url'];
        $this->params['notify_url']   = $this->config['notify_url'];

        $this->params['sign']         = $this->getSign($this->params);

        $url = 'https://openapi.alipay.com/gateway.do';
        $result = $this->postCurl($this->toString(), $url);


        // return $this->buildRequestForm();
    }

    /**
     * 将array转为json
     * @access private
     * @param
     * @return array
     */
    private function toString()
    {
        $str = '';
        foreach ($this->params as $key => $value) {
            if ($value != '') {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }
        return trim($str, '&');
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @access private
     * @param  string  $json   需要post的json数据
     * @param  string  $_url    url
     * @return mixed
     */
    private function postCurl($_params, $_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $_url);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_params);
        $headers = ['content-type: application/x-www-form-urlencoded;charset=' . $this->config['charset']];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
     * 生成签名
     * @access private
     * @param  array $_params
     * @return 加密签名
     */
    private function getSign($_params)
    {
        ksort($_params);

        $to_be_signed = '';
        foreach ($_params as $key => $value) {
            if (!in_array($key, ['sign', 'sign_type']) && !is_array($value) && $value != '') {
                $to_be_signed .= $key . '=' . $value . '&';
            }
        }
        $to_be_signed = trim($to_be_signed, '&');

        $pri_key = file_get_contents($this->config['rsa_file_path']);

        $pri_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($pri_key, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        $res = openssl_get_privatekey($pri_key);

        if (!$res) {
            die('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        if ($this->config['sign_type'] == 'RSA2') {
            openssl_sign($to_be_signed, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($to_be_signed, $sign, $res);
        }

        openssl_free_key($res);

        return base64_encode($sign);
    }
}
