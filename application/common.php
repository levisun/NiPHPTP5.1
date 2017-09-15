<?php
/**
 *
 * 应用公共文件
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: common.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */

/**
 * 实例化Logic
 * @param string $name         Logic名称
 * @param string $layer        业务层名称
 * @param bool   $appendSuffix 是否添加类名后缀
 * @return \think\Model
 */
function logic($name = '', $layer = 'logic', $appendSuffix = false)
{
    return model($name, $layer, $appendSuffix);
}

/**
 * 过滤XSS
 * @param  string $data
 * @return string
 */
function escape_xss($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[escape_xss($key)] = escape_xss($value);
        }
        return $data;
    }

    $pattern = [
        '/<\?php(.*?)\?>/si',
        '/<\?(.*?)\?>/si',
        '/<%(.*?)%>/si',
        '/<\?php|<\?|\?>|<%|%>/si',

        '/on([a-zA-Z]*?)(=)["|\'](.*?)["|\']/si',
        '/(javascript:)(.*?)(\))/si',
        '/<\!--.*?-->/si',
        '/<(\!.*?)>/si',

        '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si',
        '/<(\/?javascript.*?)>/si',

        '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',
        '/<(\/?vbscript.*?)>/si',

        '/<(expression.*?)>(.*?)<(\/expression.*?)>/si',
        '/<(\/?expression.*?)>/si',

        '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',
        '/<(\/?applet.*?)>/si',

        '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',
        '/<(\/?xml.*?)>/si',

        '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',
        '/<(\/?blink.*?)>/si',

        '/<(link.*?)>(.*?)<(\/link.*?)>/si',
        '/<(\/?link.*?)>/si',

        '/<(script.*?)>(.*?)<(\/script.*?)>/si',
        '/<(\/?script.*?)>/si',

        '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',
        '/<(\/?embed.*?)>/si',

        '/<(object.*?)>(.*?)<(\/object.*?)>/si',
        '/<(\/?object.*?)>/si',

        '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',
        '/<(\/?iframe.*?)>/si',

        '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',
        '/<(\/?frame.*?)>/si',

        '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si',
        '/<(\/?frameset.*?)>/si',

        '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',
        '/<(\/?ilayer.*?)>/si',

        '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',
        '/<(\/?layer.*?)>/si',

        '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',
        '/<(\/?bgsound.*?)>/si',

        '/<(title.*?)>(.*?)<(\/title.*?)>/si',
        '/<(\/?title.*?)>/si',

        '/<(base.*?)>(.*?)<(\/base.*?)>/si',
        '/<(\/?base.*?)>/si',

        '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',
        '/<(\/?meta.*?)>/si',

        '/<(style.*?)>(.*?)<(\/style.*?)>/si',
        '/<(\/?style.*?)>/si',

        '/<(html.*?)>(.*?)<(\/html.*?)>/si',
        '/<(\/?html.*?)>/si',

        '/<(head.*?)>(.*?)<(\/head.*?)>/si',
        '/<(\/?head.*?)>/si',

        '/<(body.*?)>(.*?)<(\/body.*?)>/si',
        '/<(\/?body.*?)>/si',
    ];

    $data = preg_replace($pattern, '', $data);

    $pattern = [
        '/[  ]+/si' => ' ',    // 多余空格
        '/[\s]+</si' => '<',   // 多余回车
        '/>[\s]+/si' => '>',

        // SQL
        '/(select)/si' => '<span>s</span>elect',
        '/(drop)/si'   => '<span>d</span>rop',
        '/(delete)/si' => '<span>d</span>elete',
        '/(create)/si' => '<span>c</span>reate',
        '/(update)/si' => '<span>u</span>pdate',
        '/(insert)/si' => '<span>i</span>nsert',

        // 特殊字符
        '/(〃|”|“)/si'  => '&quot;',
        '/(￥)/si'      => '&yen;',
        '/(—|－|～)/si' => '-',
        '/(\*)/si'      => '&lowast;',
        '/(`)/si'       => '&acute;',
        '/(™)/si'       => '&trade;',
        '/(®)/si'       => '&reg;',
        '/(©)/si'       => '&copy;',
        '/(’|‘)/si'     => '&acute;',
        '/(×)/si'       => '&times;',
        '/(÷)/si'       => '&divide;',
        '/à|á|å|â|ä/si' => 'a',
        '/è|é|ê|ẽ|ë/si' => 'e',
        '/ì|í|î/si'     => 'i',
        '/ò|ó|ô|ø/si'   => 'o',
        '/ù|ú|ů|û/si'   => 'u',
        '/ç|č/si'       => 'c',
        '/ñ|ň/si'       => 'n',
        '/ľ/si'         => 'l',
        '/ý/si'         => 'y',
        '/ť/si'         => 't',
        '/ž/si'         => 'z',
        '/š/si'         => 's',
        '/æ/si'         => 'ae',
        '/ö/si'         => 'oe',
        '/ü/si'         => 'ue',
        '/Ä/si'         => 'Ae',
        '/Ü/si'         => 'Ue',
        '/Ö/si'         => 'Oe',
        '/ß/si'         => 'ss',

    ];
    $data = preg_replace(array_keys($pattern), array_values($pattern), $data);

    // 全角转半角
    $strtr = [
        // '\'' => '&#39;', '"' => '&quot;', '<' => '&lt;', '>' => '&gt;',
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5',
        '６' => '6', '７' => '7', '８' => '8', '９' => '9',

        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F',
        'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L',
        'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R',
        'Ｓ' => 'S', 'Ｔ' => 'T', 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X',
        'Ｙ' => 'Y', 'Ｚ' => 'Z',

        'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f',
        'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l',
        'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r',
        'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
        'ｙ' => 'y', 'ｚ' => 'z',

        '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[', '】' => ']',
        '〖' => '[', '〗' => ']', '｛' => '{', '｝' => '}', '％' => '%', '＋' => '+',
        '：' => ':', '？' => '?', '！' => '!',
        '…' => '...', '‖' => '|', '｜' => '|', '　' => '',
        ];

    $data = strtr($data, $strtr);

    // 个性字符过虑
    $rule = '/[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s\_\-\(\)\[\]\{\}\|\?\/\!\@\#\$\%\^\&\+\=\:\;\"\'\<\>\,\.\，\。\《\》\\\\]+/u';
    $data = preg_replace($rule, '', $data);

    return $data;
}
