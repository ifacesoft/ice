<?php
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

$optionExcel = $component->getOption('excel', []);

if (array_key_exists('rowVisible', $optionExcel)) {
    $sheet->getRowDimension($render->getIndex())->setVisible($optionExcel['rowVisible']);
}

if (array_key_exists('columnVisible', $optionExcel)) {
    $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
}

$cell = $render->getColumn() . $render->getIndex();

$sheet->setCellValue($cell, strip_tags($component->getValue()));

$sheet->getStyle($cell)->applyFromArray($cellStyle);

$sheet->getRowDimension($render->getIndex())->setRowHeight(-1);

$render->indexInc();