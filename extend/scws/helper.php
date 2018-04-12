<?php
function pscws($_text)
{
    $pscws4 = new \scws\PSCWS4;
    $pscws4->set_dict(__DIR__ . 'extend/scws/lib/dict.utf8.xdb');
    $pscws4->send_text($_text);
    $tag = array();
    while ($some = $pscws4->get_result()) {
        foreach ($some as $key => $value) {
            $tag[] = $value['word'];
        }
    }
    return $tag;
}
