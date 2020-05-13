<?php
/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 * */
$sheet = $render->getSheet();

$sheet->setCellValue($render->getColumn() . $render->getIndex(), html_entity_decode($component->getValue()));

$render->indexInc();