<?php
/**
 * @var $render \Ice\Render\External_PHPExcel
 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
 * */
$sheet = $render->getSheet();

foreach (reset($result) as $part) {
    $optionExcel = $part->getOption('excel', []);

    if (array_key_exists('rowVisible', $optionExcel)) {
        $sheet->getRowDimension($render->getIndex())->setVisible($optionExcel['rowVisible']);
    }

    if (array_key_exists('columnVisible', $optionExcel)) {
        $sheet->getColumnDimension($render->getColumn())->setVisible($optionExcel['columnVisible']);
    }

    $part->render($render);
}