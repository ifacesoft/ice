<?php
$cellStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 14,
        'name' => 'Verdana'
    ]
];

foreach ($options['widget']->getParts() as $name => $part) {

    $label = isset($part['options']['label']) ? $part['options']['label'] : $name;

    if (isset($resource) && $resource instanceof Ice\Core\Resource) {
        $label = $resource->get($label);
    }

//    $sheet->setCellValue($column++ . ($index + (isset($options['indexOffset']) ? $options['indexOffset'] : 0)), $label);


    $col = $column++;
    $index = ($index + (isset($options['indexOffset']) ? $options['indexOffset'] : 0));

    /** @var PHPExcel_Worksheet $sheet */
    $sheet->setCellValue($col . $index, $label);
    $sheet->getStyle($col . $index)->applyFromArray($cellStyle);
    $sheet->getRowDimension($index)->setRowHeight(16);
}
