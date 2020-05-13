<?php
/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 * */
$sheet = $render->getSheet();

$optionExcel = $component->getOption('excel', []);

if (!empty($optionExcel['disable'])) {
    return;
}

if (array_key_exists('rowVisible', $optionExcel)) {
    $sheet->getRowDimension($render->getIndex())->setVisible($optionExcel['rowVisible']);
}

if (array_key_exists('columnVisible', $optionExcel)) {
    $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
}

$cell = $render->getColumn() . $render->getIndex();

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getCell($cell)->getHyperlink()->setUrl($component->getHref());

$render->columnInc();