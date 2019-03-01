<?php
/**
 *
 * 事件定义文件
 *
 * @package   NiPHP
 * @category  app
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */

return [
    'bind'      => [
    ],
    'listen'    => [
        'AppInit'      => [
            'think\listener\LoadLangPack',
            'think\listener\RouteCheck',
        ],
        'AppBegin'     => [
            'think\listener\CheckRequestCache',
        ],
        'ActionBegin'  => [],
        'AppEnd'       => [
            'app\library\Garbage',
            'app\library\Accesslog'
        ],
        'LogLevel'     => [],
        'LogWrite'     => [],
        'ResponseSend' => [],
        'ResponseEnd'  => [],
    ],
    'subscribe' => [
    ],
];
