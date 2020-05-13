<?php

use PhpOffice\PhpSpreadsheet\Style\Border;

$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        )
    ),
    'font' => array(
        'size' => 12,
        'bold' => true,
    ),
//    'fill' => array(
//        'type' => Fill::FILL_SOLID,
//        'color' => array('rgb' => '808080')
//    )
);

$startCell = $render->getColumn() . $render->getIndex();

$maxColumn = $render->getColumn();

/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
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

    if (array_key_exists('rowVisible', $optionExcel)) {
        $sheet->getRowDimension($render->getIndex())->setVisible($optionExcel['rowVisible']);
    }

    if (array_key_exists('columnVisible', $optionExcel)) {
        $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
    }

    $cell = $render->getColumn() . $render->getIndex();
    $finishCell = $render->decrementLetter($render->columnInc($colspan)) . $render->getIndex();

    if ($cell != $finishCell) {
        $sheet->mergeCells($cell . ':' . $finishCell);
    }

    $sheet->setCellValue($cell, $part->getLabel());
}

$maxColumn = $render->getColumn();

$render->indexInc();

$finishCell = $render->decrementLetter($maxColumn) . ($render->getIndex() - 1);

$sheet->getStyle($startCell . ':' . $finishCell)->applyFromArray($styleArray);
