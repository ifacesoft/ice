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

foreach ($options['widget']->getParts() as $name => $part) {
    $label = isset($part['options']['label']) ? $part['options']['label'] : $name;

    if (isset($resource) && $resource instanceof Ice\Core\Resource) {
        $label = $resource->get($label);
    }

    /** @var PHPExcel_Worksheet $sheet */
    $sheet->setCellValue($options['column'] . $options['index'], html_entity_decode($label));
    $sheet->getStyle($options['column'] . $options['index'])->applyFromArray($cellStyle);
    $sheet->getRowDimension($options['index'])->setRowHeight(16);

    $options['column']++;
}
