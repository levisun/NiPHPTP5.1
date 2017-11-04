<?php

function readerExcel($file_name)
{
    $info = pathinfo($file_name);
    if ($info['extension'] == 'xlsx') {
        $type = 'Excel2007';
    } else {
        $type = 'Excel5';
    }

    $reader = PHPExcel_IOFactory::createReader($type);

    // 载入Excel文件
    $php_excel = $reader->load($file_name);

    // 取得总行数与列数
    $worksheet      = $php_excel->getActiveSheet();
    $highest_row    = $worksheet->getHighestRow();
    $highest_column = $worksheet->getHighestColumn();
    $highest_column = PHPExcel_Cell::columnIndexFromString($highest_column);

    $data = array();
    for ($row=1; $row <= $highest_row ; $row++) {
        for ($col=0; $col < $highest_column; $col++) {
            $data[$row][$col] =
            $worksheet->getCellByColumnAndRow($col, $row)
            ->getValue();
        }
    }

    return $data;
}

function createExcel($file_name, $data)
{
    $clo = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $php_excel = new PHPExcel;

    foreach ($data as $key => $value) {
        $num = $key + 1;
        foreach ($value as $k => $val) {
            $php_excel->setActiveSheetIndex(0)
            ->setCellValue($clo[$k] . $num, $val);
        }
    }

    $writer = PHPExcel_IOFactory::createWriter($php_excel, 'Excel5');
    $writer->save($file_name);
}


/**
 * 采集
 * @param  string $url     链接
 * @param  array  $form    POST提交数据
 * @param  string $charset 编码
 * @param  array  $headers 头部信息
 * @return string
 */
function snoopy($url, $form = array(), $charset = '', $headers = array())
{
    $snoopy = new \util\Snoopy;
    $agent = array(
        'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',
        'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',
        'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 6 Build/LYZ28E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36',

        'Mozilla/5.0 (BB10; Touch) AppleWebKit/537.1+ (KHTML, like Gecko) Version/10.0.0.1337 Mobile Safari/537.1+',
        'Mozilla/5.0 (MeeGo; NokiaN9) AppleWebKit/534.13 (KHTML, like Gecko) NokiaBrowser/8.5.0 Mobile Safari/534.13',
        'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.187 Mobile Safari/534.11+',
        'Mozilla/5.0 (iPad; CPU OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5',
        'Mozilla/5.0 (iPad; CPU OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
        'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
        'Mozilla/5.0 (Linux; Android 4.1.2; Nexus 7 Build/JZ054K) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Safari/535.19',
        'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19',
    );
    $key = array_rand($agent, 1);
    $snoopy->agent = $agent[$key];
    $snoopy->headers = $headers;

    if (!empty($form)) {
        $snoopy->submit($url, $form);
    } else {
        $snoopy->fetch($url);
    }

    $result = $snoopy->results;

    if ($charset != '' && strtoupper($charset) == 'UTF-8') {
        $result = iconv('GB2312', 'UTF-8//IGNORE', $result);
    }

    return $result;
}
