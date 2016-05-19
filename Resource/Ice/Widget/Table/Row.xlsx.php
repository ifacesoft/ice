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

$cell = $render->getColumn() . $render->getIndex();

foreach ($component->getOption('row', []) as $key => $col) {
    if (is_string($key)) {
        $value = $key;

        $sheet->mergeCells($cell . ':' . $render->columnInc($col) . $render->getIndex());
    } else {
        $value = $col;
        $render->columnInc();
    }

    /** @var PHPExcel_Worksheet $sheet */
    $sheet->setCellValue($cell, $value);
    $sheet->getStyle($cell)->applyFromArray($cellStyle);
    $sheet->getRowDimension($render->getIndex())->setRowHeight(16);
}