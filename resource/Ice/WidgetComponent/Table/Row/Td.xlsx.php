<?php
$option = $component->getOption();
$optionExcel = isset($option['excel']) ? $option['excel'] : [];

$cellStyle = [
    'font' => [
        'bold' => isset($optionExcel['cell']['font']['bold']) ? $optionExcel['cell']['font']['bold'] : false,
    ]
];

$colspan = isset($option['colspan']) ? $option['colspan'] : 1;

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 */
$sheet = $render->getSheet($colspan);

$cell = $render->getColumn() . $render->getIndex();
$finishCell = $render->decrementLetter($render->columnInc($colspan)) . $render->getIndex();
if ($cell != $finishCell) {
    $sheet->mergeCells($cell . ':' . $finishCell);
}

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getStyle($cell)->applyFromArray($cellStyle);

$sheet->getStyle($cell)->getAlignment()->setWrapText(true);

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);
