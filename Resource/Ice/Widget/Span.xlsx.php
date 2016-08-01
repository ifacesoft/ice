<?php
/**
* @var $render \Ice\Render\External_PHPExcel
* @var PHPExcel_Worksheet $sheet
* */
$sheet = $render->getSheet();

$sheet->setCellValue($render->getColumn() . $render->getIndex(), html_entity_decode($component->getValue()));