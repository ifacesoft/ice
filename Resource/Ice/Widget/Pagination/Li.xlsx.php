<?php
/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var PHPExcel_Worksheet $sheet
 * */
$sheet = $render->getSheet();

$cell = $render->getColumn() . $render->getIndex();

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$render->columnInc();