<?php
function vicword($_string)
{
    ini_set('memory_limit', '512M');

    define('_VIC_WORD_DICT_PATH_', __DIR__ . 'extend/vicword/data/dict.igb');
    $fc = new \vicword\VicWord('igb');
    $result = $fc->getAutoWord($_string);
    $tag = array();
    foreach ($result as $key => $value) {
        $len = mb_strlen($value[0], 'utf8');
        if ($len <= 4 && $len > 1) {
            $tag[] = $value[0];
        }

    }
    return $tag;
}
