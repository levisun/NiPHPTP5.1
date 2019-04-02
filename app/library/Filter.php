<?php
/**
 *
 * 服务层
 * 数据安全过滤类
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\exception\HttpException;

class Filter
{

    public static function default($_data, $_strict = true)
    {
        if (is_array($_data)) {
            foreach ($_data as $key => $value) {
                $_data[$key] = self::default($value);
            }
        } elseif (is_object($_data)) {
            # code...
            return $_data;
        } else {
            // 过滤多余空格与回车
            $_data = trim($_data);
            $_data = self::ENTER($_data);
            // XSS跨域攻击
            $_data = self::XSS($_data);
            // XXE实体扩展攻击
            $_data = self::XXE($_data, $_strict);
            // 过滤PHP危害函数方法
            $_data = self::FUN($_data);
        }

        return $_data;
    }

    /**
     * 过滤多余空格与回车
     * @access public
     * @param  string $_data
     * @return string
     */
    public static function ENTER(string $_data): string
    {
        $pattern = [
            '/( ){2,}/si'    => '',
            '/>(\n|\r|\f)+/si' => '>',
            '/(\n|\r|\f)+</si' => '<',

            '/\}(\n|\r|\f)+/si' => '}',
            '/\{(\n|\r|\f)+/si' => '{',
            // '/;(\n|\r|\f)+/si' => ';',
            '/\)(\n|\r|\f)+/si' => ')',
        ];
        return preg_replace(array_keys($pattern), array_values($pattern), $_data);
    }

    /**
     * XSS
     * 跨站脚本攻击
     * @access public
     * @param  string $_data
     * @return string
     */
    public static function XSS(string $_data): string
    {
        return preg_replace([
            '/on([a-zA-Z0-9]*?)([ ]*?=[ ]*?)["|\'](.*?)["|\']/si',
            '/(javascript:)(.*?)(\))/si',
            '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si', '/<(\/?javascript.*?)>/si',
            '/<(script.*?)>(.*?)<(\/script.*?)>/si',         '/<(\/?script.*?)>/si',
            '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',         '/<(\/?applet.*?)>/si',
            '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',     '/<(\/?vbscript.*?)>/si',
            '/<(expression.*?)>(.*?)<(\/expression.*?)>/si', '/<(\/?expression.*?)>/si',
        ], '', $_data);
    }

    /**
     * XXE
     * XML 实体扩展攻击
     * @access public
     * @param  string $_data
     * @return string
     */
    public static function XXE(string $_data, bool $_strict = true): string
    {
        libxml_disable_entity_loader(true);

        $_data = preg_replace([
            // 过滤HTML嵌入
            '/<(html.*?)>(.*?)<(\/html.*?)>/si',         '/<(\/?html.*?)>/si',
            '/<(head.*?)>(.*?)<(\/head.*?)>/si',         '/<(\/?head.*?)>/si',
            '/<(title.*?)>(.*?)<(\/title.*?)>/si',       '/<(\/?title.*?)>/si',
            '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',         '/<(\/?meta.*?)>/si',
            '/<(body.*?)>(.*?)<(\/body.*?)>/si',         '/<(\/?body.*?)>/si',
            '/<(style.*?)>(.*?)<(\/style.*?)>/si',       '/<(\/?style.*?)>/si',
            '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',     '/<(\/?iframe.*?)>/si',
            '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',       '/<(\/?frame.*?)>/si',
            '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si', '/<(\/?frameset.*?)>/si',
            '/<(base.*?)>(.*?)<(\/base.*?)>/si',         '/<(\/?base.*?)>/si',

            // 过滤HTML危害标签信息
            '/<(object.*?)>(.*?)<(\/object.*?)>/si',     '/<(\/?object.*?)>/si',
            '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',           '/<(\/?xml.*?)>/si',
            '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',       '/<(\/?blink.*?)>/si',
            '/<(link.*?)>(.*?)<(\/link.*?)>/si',         '/<(\/?link.*?)>/si',
            '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',       '/<(\/?embed.*?)>/si',
            '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',     '/<(\/?ilayer.*?)>/si',
            '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',       '/<(\/?layer.*?)>/si',
            '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',   '/<(\/?bgsound.*?)>/si',
            // '/<(form.*?)>(.*?)<(\/form.*?)>/si',         '/<(\/?form.*?)>/si',
        ], '', $_data);

        // HTML转实体
        $_data = $_strict ? strip_tags($_data) : htmlspecialchars($_data);

        return $_data;
    }

    /**
     * 过滤PHP危害函数方法
     * @param  string $_data
     * @return string
     */
    public static function FUN(string $_data): string
    {
        $pattern = [
            '/(base64_decode)/si'        => 'ba&#115;e64_decode',
            '/(call_user_func_array)/si' => 'cal&#108;_user_func_array',
            '/(chown)/si'                => 'ch&#111;wn',
            '/(eval)/si'                 => 'ev&#97;l',
            '/(exec)/si'                 => 'ex&#101;c',
            '/(passthru)/si'             => 'pa&#115;sthru',
            // '/(php)/si'                  => 'ph&#112;',
            '/(phpinfo)/si'              => 'ph&#112;info',
            '/(proc_open)/si'            => 'pr&#111;c_open',
            '/(popen)/si'                => 'po&#112;en',
            '/(shell_exec)/si'           => 'sh&#101;ll_exec',
            '/(system)/si'               => 'sy&#115;tem',

            // '/(\()/si'                   => '&#40;',
            // '/(\))/si'                   => '&#41;',
        ];
        return preg_replace(array_keys($pattern), array_values($pattern), $_data);
    }
}
