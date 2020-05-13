<?php

use PhpOffice\PhpSpreadsheet\Style\Border;

$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => array('rgb' => '000000')
        )
    ),
    'font' => array(
        'size' => 14,
        'bold' => true,
    ),
//    'fill' => array(
//        'type' => Fill::FILL_SOLID,
//        'color' => array('rgb' => '808080')
//    )
);
//
//$cellStyle = [
//    'font' => [
//        'bold' => false,
//        'color' => ['rgb' => '000000'],
//        'size' => 14,
//        'name' => 'Verdana'
//    ]
//];

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 * */
$sheet = $render->getSheet();

$startCell = $render->getColumn() . $render->getIndex();

foreach ($component->getOption('row', []) as $key => $col) {
    $cell = $render->getColumn() . $render->getIndex();

    $option = [];
    $optionExcel = [];

    if (is_array($col)) {
        $option = $col;
        $col = isset($option['colspan']) ? $option['colspan'] : 1;
    }

    if (isset($option['excel'])) {
        $optionExcel = $option['excel'];
    }

    if (!empty($optionExcel['width'])) {
        $sheet->getColumnDimension($render->getColumn())->setWidth($optionExcel['width']);
    }

    if (array_key_exists('columnVisible', $optionExcel)) {
        $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
    }

    if (!is_int($key)) {
        $value = $key;
        $sheet->mergeCells($cell . ':' . $render->decrementLetter($render->columnInc($col)) . $render->getIndex());
    } else {
        $value = $col;
        $col = 1;
    }

    if ($value instanceof \Ice\Core\WidgetComponent) {
        $value = $value->render($render);
    } else{
        $sheet->setCellValue($cell, $value);
        $render->columnInc($col);
    }

//    $sheet->getStyle($cell)->applyFromArray($cellStyle);
}

$maxColumn = $render->getColumn();

$finishCell = $render->decrementLetter($maxColumn) . ($render->getIndex());

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);

$sheet->getStyle($startCell . ':' . $finishCell)->applyFromArray($styleArray);

$render->indexInc();