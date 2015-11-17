<?php
$cellStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 18,
        'name' => 'Verdana'
    ]
];

/** @var PHPExcel_Worksheet $sheet */
$sheet->setCellValue($column . $index, $label);
$sheet->getStyle($column . $index)->applyFromArray($cellStyle);
$sheet->getRowDimension($index)->setRowHeight(20);