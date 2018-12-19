<?php
/**
 *
 * 数据安全过滤 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\common\logic;

class SafeFilter
{

    /**
     * 安全过滤
     * @access public
     * @param  mixed   $_data
     * @param  boolean $_strict 严格过滤
     * @return mixed
     */
    public function filter($_data, $_strict = true)
    {
        if (is_array($_data)) {
            foreach ($_data as $key => $value) {
                $_data[$key] = $this->filter($value, $_strict);
            }
        } else {
            $_data = trim($_data);
            $_data = $this->enter($_data);
            $_data = $this->PHP($_data);
            $_data = $this->XSS($_data);
            $_data = $this->XXE($_data);
            $_data = $this->SQL($_data, $_strict);
            $_data = $this->strToEncode($_data);
        }

        return $_data;
    }

    /**
     * 转化全角字符
     * 转化特殊字符
     * @access private
     * @param  string $_content
     * @return string
     */
    private function strToEncode($_content)
    {
        $pattern = [
            // 全角转半角
            '０'=>'0','１'=>'1','２'=>'2','３'=>'3','４'=>'4','５'=>'5','６'=>'6','７'=>'7','８'=>'8','９'=>'9','Ａ'=>'A','Ｂ'=>'B','Ｃ'=>'C','Ｄ'=>'D','Ｅ'=>'E','Ｆ'=>'F','Ｇ'=>'G','Ｈ'=>'H','Ｉ'=>'I','Ｊ'=>'J','Ｋ'=>'K','Ｌ'=>'L','Ｍ'=>'M','Ｎ'=>'N','Ｏ'=>'O','Ｐ'=>'P','Ｑ'=>'Q','Ｒ'=>'R','Ｓ'=>'S','Ｔ'=>'T','Ｕ'=>'U','Ｖ'=>'V','Ｗ'=>'W','Ｘ'=>'X','Ｙ'=>'Y','Ｚ'=>'Z','ａ'=>'a','ｂ'=>'b','ｃ'=>'c','ｄ'=>'d','ｅ'=>'e','ｆ'=>'f','ｇ'=>'g','ｈ'=>'h','ｉ'=>'i','ｊ'=>'j','ｋ'=>'k','ｌ'=>'l','ｍ'=>'m','ｎ'=>'n','ｏ'=>'o','ｐ'=>'p','ｑ'=>'q','ｒ'=>'r','ｓ'=>'s','ｔ'=>'t','ｕ'=>'u','ｖ'=>'v','ｗ'=>'w','ｘ'=>'x','ｙ'=>'y','ｚ'=>'z','〔'=>'[','【'=>'[','〖'=>'[','〕'=>']','】'=>']','〗'=>']','＋' => '+','！' => '!','｜' => '|','〃' => '"','＂' => '"','－' => '-','～' => '~','…' => '...','（' => '(','）' => ')','｛' => '{','｝' => '}','？' => '?','％' => '%','：' => ':',

            // 特殊字符
            '‖' => '&#124;',
            '“' => '&ldquo;', '”' => '&rdquo;',
            '‘' => '&lsquo;', '’' => '&rsquo;',
            '™' => '&trade;', '®' => '&reg;', '©' => '&copy;', '￥' => '&yen;', '℃' => '&#8451;', '℉' => '&#8457;',
            '+' => '&#43;', '—' => '&ndash;', '×' => '&times;', '÷' => '&divide;',
        ];
        return str_replace(array_keys($pattern), array_values($pattern), $_content);
    }

    /**
     * 过滤数据
     * 回车与空格
     * @access private
     * @param  string $_content
     * @return string
     */
    private function enter($_content)
    {
        $pattern = [
            '/( ){2,}/si'    => '',
            '/[\r\n\f]+</si' => '<',
            '/>[\r\n\f]+/si' => '>',
        ];
        return preg_replace(array_keys($pattern), array_values($pattern), $_content);
    }

    /**
     * SQL
     * 数据库注入
     * @access private
     * @param  string  $_content
     * @param  boolean $_strict  过滤HTML标签
     * @return string
     */
    private function SQL($_content, $_strict)
    {
        $_content = $_strict ? strip_tags($_content) : $_content;
        $_content = htmlspecialchars($_content);

        // $_content = get_magic_quotes_gpc() === false ? addslashes($_content) : $_content;

        if ($_strict) {
            $pattern = [
                // 'and '   => '&#97;nd ',
                // 'create' => '&#99;reate',
                // 'delete' => '&#100;elete',
                // 'update' => '&#117;pdate',
                // 'or '    => '&#111;r ',

                // 安全字符
                '`'  => '&acute;',
                '~'  => '&#126;',
                '!'  => '&#33;',
                '='  => '&#61;',
                '|'  => '&#124;',
                '”'  => '&quot;',
                '“'  => '&quot;',
                '?'  => '&#129;',
                '|'  => '&#124;',
                '*'  => '&#42;',
                '\\' => '&#92;',
                '‚'  => '&sbquo;',
                '^'  => '&#94;',
                '@'  => '&#64;',
                '\'' => '&quot;',

                /*'/(#)+/si'   => '&#35;',   '/(\!)+/si'  => '&#33;',
                '/(\?)+/si'  => '&#129;',  '/(\|)+/si'  => '&#124;',
                '/(\*)+/si'  => '&#42;',   '/(`)+/si'   => '&acute;',
                '/(\\\)+/si' => '&#92;',   '/(~)+/si'   => '&#126;',
                '/(‚)+/si'   => '&sbquo;', '/(\^)+/si'  => '&#94;',
                '/(\@)+/si'  => '&#64;',   '/(=)+/si'   => '&#61;',*/
            ];

            $_content = str_replace(array_keys($pattern), array_values($pattern), $_content);

        }

        return $_content;
    }

    /**
     * XXE
     * XML 实体扩展攻击
     * @access private
     * @param  string $_content
     * @return string
     */
    private function XXE($_content)
    {
        libxml_disable_entity_loader(true);

        return preg_replace([
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
            '/<(form.*?)>(.*?)<(\/form.*?)>/si',         '/<(\/?form.*?)>/si',

            '/<\!--.*?-->/si',
        ], '', $_content);
    }

    /**
     * XSS
     * 跨站脚本攻击
     * @access private
     * @param  string $_content
     * @return string
     */
    private function XSS($_content)
    {
        return preg_replace([
            '/on([a-zA-Z0-9 ]*?)(=[ ]*?)["|\'](.*?)["|\']/si',
            '/(javascript:)(.*?)(\))/si',
            '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si', '/<(\/?javascript.*?)>/si',
            '/<(script.*?)>(.*?)<(\/script.*?)>/si',         '/<(\/?script.*?)>/si',
            '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',         '/<(\/?applet.*?)>/si',
            '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',     '/<(\/?vbscript.*?)>/si',
            '/<(expression.*?)>(.*?)<(\/expression.*?)>/si', '/<(\/?expression.*?)>/si',
        ], '', $_content);
    }

    /**
     * PHP
     * PHP脚本攻击
     * @access private
     * @param  string $_content
     * @return string
     */
    private function PHP($_content)
    {
        return preg_replace([
            '/<\?php(.*?)\?>/si',
            '/<\?(.*?)\?>/si',
            '/<%(.*?)%>/si',
            '/<\?php|<\?|\?>|<%|%>/si',
        ], '', $_content);
    }
}
