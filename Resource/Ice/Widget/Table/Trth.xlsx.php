<?php
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
            'color' => array('rgb' => '000000')
        )
    ),
    'font' => array(
        'size' => 14,
        'bold' => true,
    ),
//    'fill' => array(
//        'type' => PHPExcel_Style_Fill::FILL_SOLID,
//        'color' => array('rgb' => '808080')
//    )
);

$startCell = $render->getColumn() . $render->getIndex();

$maxColumn = $render->getColumn();

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var PHPExcel_Worksheet $sheet
 * */
$sheet = $render->getSheet();

$count = 0;

foreach ($component->getWidget()->getParts() as $part) {
    $optionExcel = $part->getOption('excel', []);

    $colspan = $part->getOption('colspan', 1);
    $count += $colspan;

    if ($count <= $component->getWidget()->getColumnCount()) {

    } else {
        break;
//        $count = $colspan;
//        $render->indexInc();
    }

    if (isset($optionExcel['width'])) {
        $sheet->getColumnDimension($render->getColumn())->setWidth($optionExcel['width']);
    }

    if (array_key_exists('columnVisible', $optionExcel)) {
        $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
    }

    $cell = $render->getColumn() . $render->getIndex();

    $sheet->setCellValue($cell, $part->getLabel());

    $render->columnInc();
}

$maxColumn = $render->getColumn();

$render->indexInc();

$finishCell = $render->decrementLetter($maxColumn) . ($render->getIndex() - 1);

$sheet->getStyle($startCell . ':' . $finishCell)->applyFromArray($styleArray);
