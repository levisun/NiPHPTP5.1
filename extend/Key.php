<?php

/**
 *
 */
class Key
{
    private $dict = [];

    public function getDict($_path = '')
    {
        if (!is_file($_path)) {
            return false;
        }

        $dict = file_get_contents($_path);
        $tree = array_chunk(explode('|', $dict), 1000);

        $len = mb_strlen($dict, mb_internal_encoding());

        echo mb_internal_encoding();
        // mb_strlen ( string $str[, string $encoding = mb_internal_encoding() ] )
    }
}
