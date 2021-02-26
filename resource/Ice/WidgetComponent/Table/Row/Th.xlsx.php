<?php
$option = $component->getOption();
$optionExcel = isset($option['excel']) ? $option['excel'] : [];

$colspan = isset($option['colspan']) ? $option['colspan'] : 1;

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 */
$sheet = $render->getSheet($colspan);

$cell = $render->getColumn() . $render->getIndex();

$column = $render->getColumn();

$finishCell = $render->decrementLetter($render->columnInc($colspan)) . $render->getIndex();
if ($cell != $finishCell) {
    $sheet->mergeCells($cell . ':' . $render->decrementLetter($render->columnInc($colspan)) . $render->getIndex());
}

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getStyle($cell)->getAlignment()->setWrapText(true);

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);
