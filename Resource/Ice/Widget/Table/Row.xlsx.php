<?php
$cellStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 14,
        'name' => 'Verdana'
    ]
];

$options['index'] = isset($options['indexOffset']) ? $options['indexOffset'] : 1;
$options['column'] = isset($options['column']) ? $options['column'] : 'A';

foreach ((array)$options['row'] as $key => $column) {
    if (is_string($key)) {
        $label = $key;

        $col = chr(ord($options['column']) + $column - 1);

        $sheet->mergeCells($options['column'] . $options['index'] . ':' . $col . $options['index']);

        $options['column'] = $col;
    } else {
        $label = $column;
    }

    /** @var PHPExcel_Worksheet $sheet */
    $sheet->setCellValue($options['column'] . $options['index'], $label);
    $sheet->getStyle($options['column'] . $options['index'])->applyFromArray($cellStyle);
    $sheet->getRowDimension($options['index'])->setRowHeight(16);

    $options['column']++;
}