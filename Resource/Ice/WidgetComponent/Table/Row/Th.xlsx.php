<?php
$option = $component->getOption();
$optionExcel = isset($option['excel']) ? $option['excel'] : [];

$colspan = isset($option['colspan']) ? $option['colspan'] : 1;

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var PHPExcel_Worksheet $sheet
 * */
$sheet = $render->getSheet($colspan);

$cell = $render->getColumn() . $render->getIndex();
$finishCell = $render->decrementLetter($render->columnInc($colspan)) . $render->getIndex();

if ($cell != $finishCell) {
    $sheet->mergeCells($cell . ':' . $finishCell);
}

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getStyle($cell)->getAlignment()->setWrapText(true);

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);
