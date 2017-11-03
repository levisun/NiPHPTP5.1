<?php
/**
 * 微信支付
 *
 * @package   NiPHPCMS
 * @category  extend\util\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: PayWechat.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/01/03
 */
/*
$config = array(
    'appid' => 'wxea53b7eabf4beb2d',
    'appsecret' => 'ac1a9edce78573f3d287f9560a2d50a7',
    'mch_id' => '1487938612',
    'key' => '0af4769d381ece7b4fddd59dcf048da6',
    'sslcert_path' => '1487938612_cert.pem',
    'sslkey_path' => '1487938612_key.pem',
);
$obj = new PayWechat($config);
$param = array(
    'body'         => '商品描述 128位',
    'detail'       => '商品详情',
    'attach'       => '附加数据 127位',
    'out_trade_no' => '商户订单号 32位 数字',
    'total_fee'    => 1000,
    'goods_tag'    => '商品标记 32位',
    'notify_url'   => '异步通知回调地址,不能携带参数',
    'respond_url'  => '同步通知回调地址,不能携带参数',
    'product_id'   => '商品ID 32位',
    'openid'       => '请求微信OPENID 必填',
);
$obj->jsPay($param);

$param = array(
    'out_trade_no' => '商户订单号 32位 数字',
    'total_fee'    => '订单金额',
    'refund_fee'   => '退款金额',
    'refund_desc'  => '退款描述',
    );
$obj->refund($param);

$params = array(
    'send_name'    => '商户名称',
    're_openid'    => '接受红包的用户',
    'total_amount' => '付款金额，单位分',
    'total_num'    => '红包发放总人数',
    'scene_id'     => '发放红包使用场景，红包金额大于200时必传',
    'wishing'      => '红包祝福语',
    'act_name'     => '活动名称',
    'remark'       => '备注',
    );
$obj->sendBonus($params);

*/
namespace util;

class PayWechat
{
    // 支付配置
    protected $config = [];

    protected $params = [];

    /**
     * 微信支付配置信息
     * @access public
     * @param  array  $config
     * @return void
     */
    public function __construct($_config)
    {
        $this->config = [
            'appid'        => $_config['appid'],
            'appsecret'    => $_config['appsecret'],
            'mch_id'       => $_config['mch_id'],
            'key'          => $_config['key'],
            'sign_type'    => !empty($_config['sign_type']) ? $_config['sign_type'] : 'md5',
            'sslcert_path' => $_config['sslcert_path'],
            'sslkey_path'  => $_config['sslkey_path'],
        ];
    }

    public function transfer($_params)
    {
        $_params = array(
            'openid'       => '用户openid',
            // NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
            'check_name'   => '校验用户姓名',
            // check_name为FORCE_CHECK时必填
            're_user_name' => '收款用户姓名',
            'amount'       => '金额',
            'desc'         => '企业付款描述信息',
        );
        $this->params = $_params;

        $this->params['mch_appid']        = $this->config['appid'];
        $this->params['mchid']            = $this->config['mch_id'];
        $this->params['nonce_str']        = $this->getNonceStr(32);
        $this->params['partner_trade_no'] = $this->config['mch_id'] . date('YmdHis') . mt_rand(111, 999);
        $this->params['spbill_create_ip'] = $this->ip(0, true);
        $this->params['sign']       = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $response = $this->postXmlCurl($this->toXml(), $url, true);
        $result = $this->formXml($response);
    }

    /**
     * 发送红包
     * @access public
     * @param  array  $_params 支付参数
     * @return string JS
     */
    public function sendBonus($_params)
    {
        $this->params = $_params;

        $this->params['nonce_str']  = $this->getNonceStr(32);
        $this->params['mch_billno'] = $this->config['mch_id'] . date('YmdHis') . mt_rand(111, 999);
        $this->params['mch_id']     = $this->config['mch_id'];
        $this->params['wxappid']    = $this->config['appid'];
        $this->params['client_ip']  = $this->ip(0, true);
        $this->params['sign']       = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $response = $this->postXmlCurl($this->toXml(), $url, true);
        $result = $this->formXml($response);

        if ($result['result_code'] == 'SUCCESS' && $result['err_code'] == 'SUCCESS') {
            $return = true;
        } else {
            $return = $result;
        }

        return $return;
    }

    /**
     * 统一下单
     * @access public
     * @param  array  $_params 支付参数
     * @return string JS
     */
    public function jsPay($_params)
    {
        // 同步通知回调地址
        $respond_url = $_params['respond_url'];
        unset($_params['respond_url']);

        $this->params = $_params;
        $this->params['trade_type']  = 'JSAPI';  // 交易类型
        $this->params['device_info'] = 'WEB';

        $result = $this->unifiedOrder();

        // 新请求参数
        $params = [
            'appId'     => $result['appid'],
            'timeStamp' => (string) time(),
            'nonceStr'  => $this->getNonceStr(32),
            'package'   => 'prepay_id=' . $result['prepay_id'],
            'signType'  => strtoupper($this->config['sign_type']),
        ];

        $params['paySign'] = $this->getSign($params);
        $js_api_parameters = json_encode($params);

        return [
            'js_api_parameters' => $js_api_parameters,
            'notify_url' => $this->params['notify_url'],
            'js' => '<script type="text/javascript">function jsApiCall(){WeixinJSBridge.invoke("getBrandWCPayRequest",' . $js_api_parameters . ',function(res){if (res.err_msg == "get_brand_wcpay_request:ok") {window.location.replace("' . $respond_url . '?out_trade_no=' . $this->params['out_trade_no'] . '");} else if (res.err_msg == "get_brand_wcpay_request:cancel") {}});}function callpay(){if (typeof WeixinJSBridge == "undefined"){if( document.addEventListener ){document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);}else if (document.attachEvent){document.attachEvent("WeixinJSBridgeReady", jsApiCall);document.attachEvent("onWeixinJSBridgeReady", jsApiCall);}}else{jsApiCall();}}</script>',
        ];
    }

    /**
     * 二维码支付
     * @access public
     * @param  array  $params 支付参数
     * @return string 二维码图片地址
     */
    public function qrcodePay($_params)
    {
        // 同步通知回调地址
        $respond_url = $_params['respond_url'];
        unset($_params['respond_url']);

        $this->params = $_params;
        $this->params['trade_type']  = 'NATIVE';  // 交易类型
        $this->params['device_info'] = 'WEB';

        $result = $this->unifiedOrder();
        $code_url = urlencode($result['code_url']);
        return 'http://paysdk.weixin.qq.com/example/qrcode.php?data=' . $code_url;
    }

    /**
     * 同步通知回调
     * @access public
     * @param
     * @return mexid
     */
    public function respond()
    {
        if (!empty($_GET['out_trade_no'])) {
            $out_trade_no = $_GET['out_trade_no'];
            $result = $this->queryOrder(['out_trade_no' => $out_trade_no]);
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' && $result['trade_state'] == 'SUCCESS') {
                $return = [
                    'out_trade_no'   => $result['out_trade_no'],    // 商户订单号
                    'openid'         => $result['openid'],          // 支付人OPENID
                    'total_fee'      => $result['total_fee'],       // 支付金额
                    'trade_type'     => $result['trade_type'],      // 支付类型
                    'transaction_id' => $result['transaction_id'],  // 微信订单号
                ];
            } else {
                $return = false;
            }
        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * 异步通知回调
     * @access public
     * @param
     * @return mexid
     */
    public function notify()
    {
        if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $result = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $result = $this->queryOrder(['out_trade_no' => $result['out_trade_no']);
                if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' && $result['trade_state'] == 'SUCCESS') {
                    $return = [
                        'out_trade_no'   => $result['out_trade_no'],    // 商户订单号
                        'openid'         => $result['openid'],          // 支付人OPENID
                        'total_fee'      => $result['total_fee'],       // 支付金额
                        'trade_type'     => $result['trade_type'],      // 支付类型
                        'transaction_id' => $result['transaction_id'],  // 微信订单号
                    ];
                } else {
                    $return = false;
                }
            } else {
                $return = false;
            }
        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * 退款操作
     * @access public
     * @param
     * @return mixed
     */
    public function refund($_params)
    {
        $this->params = $_params;

        $this->params['appid']         = $this->config['appid'];
        $this->params['mch_id']        = $this->config['mch_id'];
        $this->params['nonce_str']     = $this->getNonceStr(32);
        $this->params['out_refund_no'] = $this->config['mch_id'] . date('YmdHis') . mt_rand(111, 999);
        $this->params['op_user_id']    = $this->config['mch_id'];
        $this->params['sign']          = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $response = $this->postXmlCurl($this->toXml(), $url, true);
        $return = $this->formXml($response);

        if ($result['return_code'] == 'FAIL') {
            $return = false;
        }

        if ($result['result_code'] == 'SUCCESS') {
            $return = true;
        } elseif ($result['err_code'] == 'TRADE_STATE_ERROR') {
            $return = true;
        } else {
            $return = false;
        }

        return $result;

        /*if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            // 退款成功
            // 订单处理业务
            return true;
        }
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'FAIL') {
            if ($result['err_code'] == 'TRADE_STATE_ERROR') {
                return '此订单已退达款，请勿重复操作';
            }
            return '退款失败';
        }
        return $result;*/
    }

    /**
     * 获得退款信息
     * @access public
     * @param
     * @return mixed
     */
    public function queryRefund($_params)
    {
        $this->params['appid']     = $this->config['appid'];
        $this->params['mch_id']    = $this->config['mch_id'];
        $this->params['nonce_str'] = $this->getNonceStr(32);

        if (!empty($_params['transaction_id'])) {
            $this->params['transaction_id'] = $_params['transaction_id'];
        }

        if (!empty($_params['out_trade_no'])) {
            $this->params['out_trade_no'] = $_params['out_trade_no'];
        }

        if (empty($this->params['transaction_id'])) {
            unset($this->params['transaction_id']);
        }

        if (empty($this->params['out_trade_no'])) {
            unset($this->params['out_trade_no']);
        }

        $this->params['sign'] = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $response = $this->postXmlCurl($this->toXml(), $url);
        $result = $this->formXml($response);
        return $result;
    }

    /**
     * 获得订单信息
     * @access public
     * @param
     * @return mixed
     */
    public function queryOrder($_params)
    {
        $this->params['appid']     = $this->config['appid'];
        $this->params['mch_id']    = $this->config['mch_id'];
        $this->params['nonce_str'] = $this->getNonceStr(32);

        if (!empty($_params['transaction_id'])) {
            $this->params['transaction_id'] = $_params['transaction_id'];
        }

        if (!empty($_params['out_trade_no'])) {
            $this->params['out_trade_no'] = $_params['out_trade_no'];
        }

        if (empty($this->params['transaction_id'])) {
            unset($this->params['transaction_id']);
        }

        if (empty($this->params['out_trade_no'])) {
            unset($this->params['out_trade_no']);
        }

        $this->params['sign'] = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $response = $this->postXmlCurl($this->toXml(), $url);
        $result = $this->formXml($response);
        return $result;
    }

    /**
     * 生成支付临时订单
     * @access private
     * @param
     * @return array
     */
    private function unifiedOrder()
    {
        $this->params['appid']            = $this->config['appid'];
        $this->params['mch_id']           = $this->config['mch_id'];
        $this->params['nonce_str']        = $this->getNonceStr(32);
        $this->params['spbill_create_ip'] = $this->ip(0, true);
        $this->params['time_start']       = date('YmdHis');
        $this->params['time_expire']      = date('YmdHis', time() + 600);
        $this->params['sign']             = $this->getSign($this->params);

        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $response = $this->postXmlCurl($this->toXml(), $url);
        $result = $this->formXml($response);
        return $result;
    }

    /**
     * 将array转为xml
     * @access private
     * @param
     * @return array
     */
    private function toXml()
    {
        $xml = '<xml>';
        foreach ($this->params as $key => $value) {
            if ($value != '' && !is_array($value)) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * 将xml转为array
     * @access private
     * @param  string $_xml
     * @return array
     */
    private function formXml($_xml)
    {
        libxml_disable_entity_loader(true);
        $data = (array) simplexml_load_string($_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $data;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @access private
     * @param  string  $_xml      需要post的xml数据
     * @param  string  $_url      请求地址url
     * @param  boolean $_use_cert 是否使用证书
     * @param  intval  $_second   url执行超时时间，默认30s
     * @return mixed
     */
    private function postXmlCurl($_xml, $_url, $_use_cert = false, $_second = 30)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, $_second);       // 设置超时
        curl_setopt($curl, CURLOPT_URL, $_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);      // 严格校验
        curl_setopt($curl, CURLOPT_HEADER, false);          // 设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   // 要求结果为字符串且输出到屏幕上
        if($_use_cert == true){
            //设置证书 使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $this->config['sslcert_path']);
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $this->config['sslkey_path']);
        }
        curl_setopt($curl, CURLOPT_POST, true);             // post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_xml);       // post传输数据
        $result = curl_exec($curl);                         // 运行curl

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

        $sign = '';
        foreach ($_params as $key => $value) {
            if (!in_array($key, ['sign', 'sslcert_path']) && !is_array($value) && $value != '') {
                $sign .= $key . '=' . $value . '&';
            }
        }
        $sign .= 'key=' . $this->config['key'];
        $sign = trim($sign, '&');
        $sign = $this->config['sign_type']($sign);

        return strtoupper($sign);
    }

    /**
     * 产生随机字符串，不长于32位
     * @access private
     * @param  intval $_length
     * @return 产生的随机字符串
     */
    private function getNonceStr($_length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $count = strlen($chars) -1;
        $string = '';
        for ($i=0; $i < $_length; $i++) {
            $string .= substr($chars, mt_rand(0, $count), 1);
        }
        return $string;
    }

    /**
     * 获取客户端IP地址
     * @access private
     * @param  integer   $_type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    private function ip($_type = 0, $_adv = true)
    {
        $_type      = $_type ? 1 : 0;
        static $ip = null;

        if (null !== $ip) {
            return $ip[$_type];
        }

        if ($_adv) {
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
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$_type];
    }
}
