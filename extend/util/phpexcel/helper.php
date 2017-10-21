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
