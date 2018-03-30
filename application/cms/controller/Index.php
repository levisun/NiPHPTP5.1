<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index
{
    public function index()
    {
        $t = '<img class="desc_anchor" id="desc-module-1" src="https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif"><p><img align="absmiddle" src="https://img.alicdn.com/imgextra/i2/938198529/TB2sMuJf_tYBeNjy1XdXXXXyVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB2kHNUXIwX61BjSspkXXaGYVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i2/938198529/TB2Y.OPaB0kpuFjSsziXXa.oVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB2gpGBaJBopuFjSZPcXXc9EpXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB2c9aGaUdnpuFjSZPhXXbChpXa_!!938198529.jpg"></p><img class="desc_anchor" id="desc-module-2" src="https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif"><p><img align="absmiddle" src="https://img.alicdn.com/imgextra/i2/938198529/TB2yr0fkYsTMeJjSszdXXcEupXa_!!938198529.jpg"></p><img class="desc_anchor" id="desc-module-3" src="https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif"><p><img align="absmiddle" src="https://img.alicdn.com/imgextra/i4/938198529/TB23mWOaCtkpuFjy0FhXXXQzFXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i2/938198529/TB2VieOaxdkpuFjy0FbXXaNnpXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i2/938198529/TB2TBaQarFlpuFjy0FgXXbRBVXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i1/938198529/TB2YYOSarRkpuFjSspmXXc.9XXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i1/938198529/TB2WfKQaCBjpuFjSsplXXa5MVXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i2/938198529/TB2ZwKIaNhmpuFjSZFyXXcLdFXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i4/938198529/TB2SDGzXskd61BjSZPhXXcb9VXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i1/938198529/TB28VWzXwQc61BjSZFGXXa1DFXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i1/938198529/TB2memJaS0mpuFjSZPiXXbssVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i2/938198529/TB2_i9FaNBmpuFjSZFsXXcXpFXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB20XKBaJBopuFjSZPcXXc9EpXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB2zeCJaHxmpuFjSZJiXXXauVXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i3/938198529/TB2QkBBbCiK.eBjSZFsXXbxZpXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i1/938198529/TB2Zh.DaY5K.eBjy0FfXXbApVXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i4/938198529/TB2OoVIbsCO.eBjSZFzXXaRiVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i2/938198529/TB2WHedXssa61BjSszcXXacMpXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i2/938198529/TB2y_yeXxwa61BjSspeXXXX9FXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i3/938198529/TB29wePar0kpuFjy0FjXXcBbVXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i1/938198529/TB2nuCKaWm5V1BjSszhXXcMtXXa_!!938198529.jpg"><img alt="" src="https://img.alicdn.com/imgextra/i1/938198529/TB2cmKFaOlnpuFjSZFgXXbi7FXa_!!938198529.jpg"></p><img class="desc_anchor" id="desc-module-4" src="https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif"><p><img align="absmiddle" src="https://img.alicdn.com/imgextra/i1/938198529/TB2wFOEaS4mpuFjSZFOXXaUqpXa_!!938198529.jpg"><img align="absmiddle" src="https://img.alicdn.com/imgextra/i1/938198529/TB2YwKKaSFmpuFjSZFrXXayOXXa_!!938198529.jpg"></p>';
        $t = preg_replace('/(src=")([a-zA-Z0-9:\/\._\!]*?)(gif")/si', '', $t);
        echo $t;
        halt(1);

        $res = new \RandBonus;
        $r = $res->getBonus(2000000, 100);
        halt($r);
        foreach ($r as $key => $value) {
            if ($value < 0) {
                echo $value;
            }
        }
        halt(array_sum($r));

        // 15307330
        return json(array(123, 333));
    }
}
