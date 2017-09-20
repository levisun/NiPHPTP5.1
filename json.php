<?php
$tmall = include('runtime/json/Tmall.php');
// print_r($tmall);
$c = array(
    array(
        'name' => '女装',
        'id_data' => array(),
    ),

    array(
        'name' => '女鞋',
        'id_data' => array(),
    ),

    array(
        'name' => '男装',
        'id_data' => array(),
    ),

    array(
        'name' => '男鞋',
        'id_data' => array(),
    ),

    array(
        'name' => '内衣',
        'id_data' => array(),
    ),

    array(
        'name' => '母婴',
        'id_data' => array(),
    ),

    array(
        'name' => '手机',
        'id_data' => array(),
    ),

    array(
        'name' => '数码',
        'id_data' => array(),
    ),

    array(
        'name' => '家电',
        'id_data' => array(),
    ),

    array(
        'name' => '美妆',
        'id_data' => array(),
    ),

    array(
        'name' => '箱包',
        'id_data' => array(),
    ),

    array(
        'name' => '运动',
        'id_data' => array(),
    ),

    array(
        'name' => '户外',
        'id_data' => array(),
    ),

    array(
        'name' => '家装',
        'id_data' => array(),
    ),

    array(
        'name' => '家纺',
        'id_data' => array(),
    ),

    array(
        'name' => '居家',
        'id_data' => array(),
    ),

    array(
        'name' => '鲜花园艺',
        'id_data' => array(),
    ),

    array(
        'name' => '饰品',
        'id_data' => array(),
    ),

    array(
        'name' => '食品',
        'id_data' => array(),
    ),

    array(
        'name' => '生鲜',
        'id_data' => array(),
    ),

    array(
        'name' => '汽车摩托',
        'id_data' => array(),
    ),

    array(
        'name' => '医药',
        'id_data' => array(),
    ),

    array(
        'name' => '图书',
        'id_data' => array(),
    ),

    array(
        'name' => '通信',
        'id_data' => array(),
    ),

    array(
        'name' => '洗护',
        'id_data' => array(),
    ),

    array(
        'name' => '乐器',
        'id_data' => array(),
    ),
);

foreach ($c as $key => $value) {
    $c[$key]['id_data']['tmall'] = $tmall[$value['name']]['id'];
}

print_r($c);