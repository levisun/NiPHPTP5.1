<?php
$config = array(
    'appid' => 'wxea53b7eabf4beb2d',
    'appsecret' => 'ac1a9edce78573f3d287f9560a2d50a7',
    'mch_id' => '1487938612',
    'key' => '0af4769d381ece7b4fddd59dcf048da6',
    'sslcert_path' => '1487938612_cert.pem',
    'sslkey_path' => '1487938612_key.pem',
);
$obj = new \payment\PayWechat($config);
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