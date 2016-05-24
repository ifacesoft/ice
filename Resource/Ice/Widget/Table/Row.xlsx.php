<?php
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb' => '000000')
        )
    ),
//    'font' => array(
//        'size' => 8,
//        'bold'  => true,
//    ),
//    'fill' => array(
//        'type' => PHPExcel_Style_Fill::FILL_SOLID,
//        'color' => array('rgb' => '808080')
//    )
);

$cellStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 14,
        'name' => 'Verdana'
    ]
];

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var PHPExcel_Worksheet $sheet
 * */
$sheet = $render->getSheet();

$startCell = $render->getColumn() . $render->getIndex();

foreach ($component->getOption('row', []) as $key => $col) {
    $cell = $render->getColumn() . $render->getIndex();

    if (is_string($key)) {
        $value = $key;

        $sheet->mergeCells($cell . ':' . $render->decrementLetter($render->columnInc($col) ) . $render->getIndex());
    } else {
        $value = $col;
        $render->columnInc();
    }

    /** @var PHPExcel_Worksheet $sheet */
    $sheet->setCellValue($cell, $value);
    $sheet->getStyle($cell)->applyFromArray($cellStyle);
    $sheet->getRowDimension($render->getIndex())->setRowHeight(16);
}

$maxColumn = $render->getColumn();

$finishCell = $render->decrementLetter($maxColumn) . ($render->getIndex() - 1);

$sheet->getStyle($startCell . ':' . $finishCell)->applyFromArray($styleArray);

$render->indexInc();