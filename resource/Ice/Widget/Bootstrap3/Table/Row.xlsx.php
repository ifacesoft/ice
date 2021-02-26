<?php

use PhpOffice\PhpSpreadsheet\Style\Border;

$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => array('rgb' => '000000')
        )
    ),
//    'font' => array(
//        'size' => 8,
//        'bold'  => true,
//    ),
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

//$sheet->getRowDimension('5')->setOutlineLevel(1);
//
//$sheet->getRowDimension('5')->setCollapsed(true);
//$sheet->getRowDimension('5')->setVisible(false);
//
//for ($i = 51; $i <= 80; $i++) {
//    $sheet->setCellValue('A'
//        .
//        $i,
//        "FName $i");
//    $sheet->setCellValue('B'
//        .
//        $i,
//        "LName $i");
//    $sheet->setCellValue('C'
//        .
//        $i,
//        "PhoneNo $i");
//    $sheet->setCellValue('D'
//        .
//        $i,
//        "FaxNo $i");
//    $sheet->setCellValue('E'
//        .
//        $i,
//        true);
//    $sheet->getRowDimension($i)->setOutlineLevel(1);
//    $sheet->getRowDimension($i)->setVisible(false);
//}
//$sheet->getRowDimension(81)->setCollapsed(true);
//$sheet->setShowSummaryBelow(false);


$columnCount = 59;
foreach ($result as $offset => $row) {
    $count = 0;

    foreach ($row as $partName => $part) {
        $colspan = $part->getOption('colspan', 1);
        $count += $colspan;

        $optionExcel = $part->getOption('excel', []);

        if ($count <= $columnCount) {

        } else {
            $count = $colspan;
            $render->indexInc();
        }

        if (!empty($optionExcel['rowOutlineLevel'])) {
            $sheet->getRowDimension($render->getIndex())->setOutlineLevel($optionExcel['rowOutlineLevel']);
        }

        if (array_key_exists('rowVisible', $optionExcel)) {
            $sheet->getRowDimension($render->getIndex())->setVisible($optionExcel['rowVisible']);
        }

//        if (array_key_exists('columnVisible', $optionExcel)) {
//            $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
//        }

        if (array_key_exists('rowCollapsed', $optionExcel)) {
            $sheet->getRowDimension($render->getIndex())->setCollapsed($optionExcel['rowCollapsed']);
        }

//        if (!empty($optionExcel['width'])) {
//            $sheet->getColumnDimension($render->getColumn())->setWidth($optionExcel['width']);
//        } else {
//            $sheet->getColumnDimension($render->getColumn())->setWidth(25);
//        }

        $part->render($render);
    }

    $maxColumn = $render->getColumn();

    $render->indexInc();
}

$finishCell = $render->decrementLetter($maxColumn) . ($render->getIndex() - 1);

$sheet->getStyle($startCell . ':' . $finishCell)->applyFromArray($styleArray);