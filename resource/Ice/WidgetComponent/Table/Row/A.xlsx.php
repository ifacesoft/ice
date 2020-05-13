<?php
/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 * */
$sheet = $render->getSheet();

$cell = $render->getColumn() . $render->getIndex();

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getCell($cell)->getHyperlink()->setUrl($component->getHref());

$sheet->getStyle($cell)->getAlignment()->setWrapText(true);

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);

$render->columnInc();