<?php

namespace Ice\Helper;

use PHPExcel_IOFactory;
use PHPExcel_RichText;

class Vendor_PHPExcel
{
    public static function saveToBytes($objPHPExcel, $type)
    {
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $type);
        ob_start();
        $objWriter->save('php://output');
        return ob_get_clean();
    }

    public static function loadFromBytes($bytes)
    {
        $file = tempnam(sys_get_temp_dir(), 'excel_');
        $handle = fopen($file, "w");
        fwrite($handle, $bytes);
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        fclose($handle);
        unlink($file);
        return $objPHPExcel;
    }

    public static function getData(\PHPExcel $objPHPExcel, $activeSheetIndex = 0)
    {
        $objPHPExcel->setActiveSheetIndex($activeSheetIndex);

        $rows = [];

        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();

            $cells = [];

            /** @var \PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();

                if ($value instanceof PHPExcel_RichText) {
                    $value = $value->getRichTextElements()[0]->getText();
                }

                array_push($cells, $value);
            }

            if (array_filter($cells)) {
                array_push($rows, $cells);
            }
        }

        return $rows;
    }
}