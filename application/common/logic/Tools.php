<?php
/**
 *
 * 工具箱 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\common\logic;

class Tools
{

    /**
     * 随机码  邀请码  兑换码
     * @access public
     * @param
     * @return string
     */
    function randomCode()
    {
        $code = sprintf('%x', crc32(microtime()));
        if (strlen($code) < 8) {
            return randomCode();
        } else {
            return $code;
        }
    }

    /**
     * 密码加密
     * @access public
     * @param  string $_password
     * @param  string $_salt
     * @return string
     */
    function md5Password($_password, $_salt)
    {
        $_password = md5(trim($_password));
        return  md5($_password . $_salt);
    }

    /**
     * 文件大小
     * @access public
     * @param  string $_size_or_path 文件大小或文件路径
     * @return string
     */
    function fileSize($_size_or_path)
    {
        if (strpos($_size_or_path, '.') !== false && is_file($_size_or_path)) {
            $_size_or_path = filesize($_size_or_path);
        }

        $unit = ['B', 'KB', 'MB', 'GB', 'TB'];

        $pos = 0;
        while ($_size_or_path >= 1024) {
            $_size_or_path /= 1024;
            $pos++;
        }

        return round($_size_or_path, 2) . ' ' . $unit[$pos];
    }

    /**
     * 模板设置参数
     * @access public
     * @param  string $_default_theme 模板主题
     * @return array
     */
    public function getTemplateConfig($_default_theme)
    {
        $template = config('template.');

        $template['view_path'] = env('root_path') . 'public' .
            DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR .
            request()->module() . DIRECTORY_SEPARATOR .
            $_default_theme . DIRECTORY_SEPARATOR;

        $template['tpl_replace_string'] = [
            '__DOMAIN__'   => request()->root(true) . '/',
            '__PHP_SELF__' => basename(request()->baseFile()),
            '__STATIC__'   => request()->root(true) . '/static/',
            '__THEME__'    => $_default_theme,
            '__CSS__'      => request()->root(true) . '/theme/' . request()->module() . '/' . $_default_theme . '/css/',
            '__JS__'       => request()->root(true) . '/theme/' . request()->module() . '/' . $_default_theme . '/js/',
            '__IMG__'      => request()->root(true) . '/theme/' . request()->module() . '/' . $_default_theme . '/images/',
        ];

        return $template;
    }

    /**
     * 安全过滤
     * @access public
     * @param  mixed   $_content
     * @param  boolean $_hs      HTML转义 默认false
     * @param  boolean $_hxp     HTML XML PHP标签过滤 默认false
     * @param  boolean $_rn      回车换行空格过滤 默认true
     * @param  boolean $_script  JS脚本过滤 默认true
     * @param  boolean $_sql     SQL关键词过滤 默认true
     * @return mixed
     */
    public function safeFilter($_content, $_hs = false, $_hxp = false, $_rn = true, $_sql = true, $_script = true)
    {
        if (is_array($_content)) {
            foreach ($_content as $key => $value) {
                $_content[trim($key)] = $this->safeFilter($value, $_hs, $_hxp, $_script, $_sql);
            }
            return $_content;
        } else {
            // 过滤前后空格
            $_content = trim($_content);

            //特殊字符过滤
            $pattern = [
                // 全角转半角
                '０'=>'0','１'=>'1','２'=>'2','３'=>'3','４'=>'4','５'=>'5','６'=>'6','７'=>'7','８'=>'8','９'=>'9','Ａ'=>'A','Ｂ'=>'B','Ｃ'=>'C','Ｄ'=>'D','Ｅ'=>'E','Ｆ'=>'F','Ｇ'=>'G','Ｈ'=>'H','Ｉ'=>'I','Ｊ'=>'J','Ｋ'=>'K','Ｌ'=>'L','Ｍ'=>'M','Ｎ'=>'N','Ｏ'=>'O','Ｐ'=>'P','Ｑ'=>'Q','Ｒ'=>'R','Ｓ'=>'S','Ｔ'=>'T','Ｕ'=>'U','Ｖ'=>'V','Ｗ'=>'W','Ｘ'=>'X','Ｙ'=>'Y','Ｚ'=>'Z','ａ'=>'a','ｂ'=>'b','ｃ'=>'c','ｄ'=>'d','ｅ'=>'e','ｆ'=>'f','ｇ'=>'g','ｈ'=>'h','ｉ'=>'i','ｊ'=>'j','ｋ'=>'k','ｌ'=>'l','ｍ'=>'m','ｎ'=>'n','ｏ'=>'o','ｐ'=>'p','ｑ'=>'q','ｒ'=>'r','ｓ'=>'s','ｔ'=>'t','ｕ'=>'u','ｖ'=>'v','ｗ'=>'w','ｘ'=>'x','ｙ'=>'y','ｚ'=>'z',
                '〔'=>'[','【'=>'[','〖'=>'[','〕'=>']','】'=>']','〗'=>']',

                '＋' => '&#43;',
                '！' => '&#33;',
                '｜' => '&#124;',
                '￥' => '&yen;',
                '〃' => '&quot;',
                '＂' => '&quot;',
                '－' => '&ndash;',
                '～' => '&#126;',
                '…' => '&#133;',
                '（' => '&#40;',
                '）' => '&#41;',
                '｛' => '&#123;',
                '｝' => '&#125;',
                '？' => '&#129;',
                '％' => '&#37;',
                '：' => '&#58;',

                // 特殊字符
                '+' => '&#43;', '—' => '&ndash;', '×' => '&times;', '÷' => '&divide;',
                '‖' => '&#124;',
                '“' => '&ldquo;', '”' => '&rdquo;',
                '‘' => '&lsquo;', '’' => '&rsquo;',
                '™' => '&trade;', '®' => '&reg;', '©' => '&copy;',
                '℃' => '&#8451;', '℉' => '&#8457;',

                // 安全字符
                '|'  => '&#124;',
                '*'  => '&#42;',
                '`'  => '&acute;',
                '\\' => '&#92;',
                '~'  => '&#126;',
                '‚'  => '&sbquo;',
                // ','  => '&#44;',
                // '.'  => '&#46;',
                '^'  => '&#94;',

                // HTML中的JS无法执行
                // '\'' => '&#039;',
                // '%'  => '&#37;',
                // '!'  => '&#33;',
                // '@'  => '&#64;',
                // '-'  => '&ndash;',
                // '?'  => '&#129;',
                // '+'  => '&#43;',
                // ':'  => '&#58;',
                // '='  => '&#61;',
                // '('  => '&#40;',
                // ')'  => '&#41;',
            ];

            $_content = str_replace(array_keys($pattern), array_values($pattern), $_content);

            // 过滤非法标签
            $_content = preg_replace([
                '/<\?php(.*?)\?>/si',
                '/<\?(.*?)\?>/si',
                '/<%(.*?)%>/si',
                '/<\?php|<\?|\?>|<%|%>/si',
            ], '', $_content);

            // 过滤JS脚本
            if ($_script === true || $_script === 'script' || $_script === 'js') {
                $_content = preg_replace([
                    '/on([a-zA-Z0-9]*?)(=)["|\'](.*?)["|\']/si',
                    '/(javascript:)(.*?)(\))/si',
                    '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si',
                    '/<(\/?javascript.*?)>/si',
                    '/<(script.*?)>(.*?)<(\/script.*?)>/si',
                    '/<(\/?script.*?)>/si',
                    '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',
                    '/<(\/?applet.*?)>/si',
                    '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',
                    '/<(\/?vbscript.*?)>/si',
                    '/<(expression.*?)>(.*?)<(\/expression.*?)>/si',
                    '/<(\/?expression.*?)>/si',
                ], '', $_content);
            }

            // 过滤SQL关键词
            if ($_sql === true || $_sql === 'sql') {
                $pattern = [
                    '/(and )/si'     => '&#97;nd ',
                    '/(between)/si'  => '&#98;etween',
                    '/(chr)/si'      => '&#99;hr',
                    '/(char)/si'     => '&#99;har',
                    '/(count )/si'   => '&#99;ount ',
                    '/(create)/si'   => '&#99;reate',
                    '/(declare)/si'  => '&#100;eclare',
                    '/(delete)/si'   => '&#100;elete',
                    '/(execute)/si'  => '&#101;xecute',
                    '/(insert)/si'   => '&#105;nsert',
                    '/(join)/si'     => '&#106;oin',
                    '/(update)/si'   => '&#117;pdate',
                    '/(master)/si'   => '&#109;aster',
                    '/(mid )/si'     => '&#109;id ',
                    '/(or )/si'      => '&#111;r ',
                    '/(select)/si'   => '&#115;elect',
                    '/(truncate)/si' => '&#116;runcate',
                    '/(where)/si'    => '&#119;here',

                    '/(\%)+/si'  => '&#37;', '/(\!)+/si'  => '&#33;',
                    /*'/(=)+/si'  => '&#61;', '/(\-)+/si' => '&ndash;',*/ '/(\+)+/si' => '&#43;', '/(\*)+/si'  => '&#42;',
                    '/(\:)+/si'  => '&#58;', '/(\()+/si'  => '&#40;', '/(\))+/si'  => '&#41;',
                    // '\'' => '&#039;',

                //
                // '@'  => '&#64;',
                //
                // '?'  => '&#129;',
                // '+'  => '&#43;',
                // ':'  => '&#58;',
                ];
                $_content = preg_replace(array_keys($pattern), array_values($pattern), $_content);
            }

            // 回车换行空格
            if ($_rn === true || $_rn === 'rn') {
                $pattern = [
                    '/( ){2,}/si'    => '',
                    '/[\r\n\f]+</si' => '<',
                    '/>[\r\n\f]+/si' => '>',
                ];
                $_content = preg_replace(array_keys($pattern), array_values($pattern), $_content);
            }

            // 过滤HTML XML PHP标签
            if ($_hxp === true || $_hxp === 'hxp') {
                $_content = strip_tags($_content);
            } else {
                $_content = preg_replace([
                    // 过滤HTML嵌入
                    '/<(html.*?)>(.*?)<(\/html.*?)>/si',
                    '/<(\/?html.*?)>/si',
                    '/<(head.*?)>(.*?)<(\/head.*?)>/si',
                    '/<(\/?head.*?)>/si',
                    '/<(title.*?)>(.*?)<(\/title.*?)>/si',
                    '/<(\/?title.*?)>/si',
                    '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',
                    '/<(\/?meta.*?)>/si',
                    '/<(body.*?)>(.*?)<(\/body.*?)>/si',
                    '/<(\/?body.*?)>/si',
                    '/<(style.*?)>(.*?)<(\/style.*?)>/si',
                    '/<(\/?style.*?)>/si',
                    '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',
                    '/<(\/?iframe.*?)>/si',
                    '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',
                    '/<(\/?frame.*?)>/si',
                    '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si',
                    '/<(\/?frameset.*?)>/si',
                    '/<(base.*?)>(.*?)<(\/base.*?)>/si',
                    '/<(\/?base.*?)>/si',

                    // 过滤HTML危害标签信息
                    '/<(object.*?)>(.*?)<(\/object.*?)>/si',
                    '/<(\/?object.*?)>/si',
                    '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',
                    '/<(\/?xml.*?)>/si',
                    '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',
                    '/<(\/?blink.*?)>/si',
                    '/<(link.*?)>(.*?)<(\/link.*?)>/si',
                    '/<(\/?link.*?)>/si',
                    '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',
                    '/<(\/?embed.*?)>/si',
                    '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',
                    '/<(\/?ilayer.*?)>/si',
                    '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',
                    '/<(\/?layer.*?)>/si',
                    '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',
                    '/<(\/?bgsound.*?)>/si',
                    '/<(form.*?)>(.*?)<(\/form.*?)>/si',
                    '/<(\/?form.*?)>/si',

                    '/<\!--.*?-->/si',
                ], '', $_content);
            }

            // HTML转义
            if ($_hs === true || $_hs === 'hs') {
                $_content = htmlspecialchars($_content);
            }
        }

        return $_content;
    }

    /**
     * 字符串加密
     * @access public
     * @param  mixed  $_str     加密前的字符串
     * @param  string $_authkey 密钥
     * @return string           加密后的字符串
     */
    public function encrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
        $_authkey = md5($_authkey . env('app_path'));
        if (is_array($_str)) {
            $en = [];
            foreach ($_str as $key => $value) {
                $en[$this->encrypt($key)] = $this->encrypt($value, $_authkey);
            }
            return $en;
        } elseif(is_bool($_str) || is_null($_str)) {
            return $_str;
        } else {
            $coded = '';
            $keylength = strlen($_authkey);
            for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
                $coded .= substr($_str, $i, $keylength) ^ $_authkey;
            }
            return str_replace('=', '', base64_encode($coded));
        }
    }

    /**
     * 字符串解密
     * @access public
     * @param  mixed  $_str     加密后的字符串
     * @param  string $_authkey 密钥
     * @return string           加密前的字符串
     */
    public function decrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
        $_authkey = md5($_authkey . env('app_path'));
        if (is_array($_str)) {
            $de = [];
            foreach ($_str as $key => $value) {
                $de[$this->decrypt($key)] = $this->decrypt($value, $_authkey);
            }
            return $de;
        } elseif(is_bool($_str) || is_null($_str)) {
            return $_str;
        } else {
            $coded = '';
            $keylength = strlen($_authkey);
            $_str = base64_decode($_str);
            for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
                $coded .= substr($_str, $i, $keylength) ^ $_authkey;
            }
            return $coded;
        }
    }
}
