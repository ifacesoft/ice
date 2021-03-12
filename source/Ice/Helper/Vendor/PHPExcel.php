<?php

namespace Ice\Helper;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Vendor_PHPExcel
{
    /**
     * @param $objPHPExcel
     * @param $type
     * @return false|string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function saveToBytes($objPHPExcel, $type)
    {
        $objWriter = IOFactory::createWriter($objPHPExcel, $type);
        ob_start();
        $objWriter->save('php://output');
        return ob_get_clean();
    }

    /**
     * @param $bytes
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function loadFromBytes($bytes)
    {
        $fileName = tempnam(sys_get_temp_dir(), 'excel_');
        $handle = fopen($fileName, 'wb');
        fwrite($handle, $bytes);
        $objPHPExcel = self::loadFromFile($fileName);
        fclose($handle);
        unlink($fileName);
        return $objPHPExcel;
    }

    /**
     * @param $fileName
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function loadFromFile($fileName)
    {
        return IOFactory::load($fileName);
    }

    /**
     * @param Spreadsheet $objPHPExcel
     * @param int $activeSheetIndex
     * @param array $columnNames
     * @param int $offsetRows
     * @return array
     * @throws Exception
     * @todo: $columnNames mast by required
     */
    public static function getData(Spreadsheet $objPHPExcel, $activeSheetIndex = 0, array $columnNames = [], $offsetRows = 0)
    {
        $objPHPExcel->setActiveSheetIndex($activeSheetIndex);

        $columnNames = Type_Array::rebuild($columnNames);

        $rows = [];

        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {

            if ($offsetRows > 0) {
                $offsetRows--;
                continue;
            }

            $cellIterator = $row->getCellIterator();

            $cells = [];

            $columnLetter = 'A';

            $columnCount = $columnNames ? count($columnNames) : 0;

            /** @var Cell $cell */
            foreach ($cellIterator as $cell) {
                $cellColumn = $cell->getColumn();

                if ($columnCount > 0 && !array_key_exists($cellColumn, $columnNames)) {
                    continue;
                }

                $value = $cell->getValue();

                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    $value = $value
                    ? Date::get(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value), Date::FORMAT_MYSQL, 'UTC')
                    : null;
                } else {
                    if ($value instanceof RichText) {
                        $value = $value->getRichTextElements()[0]->getText();
                    }

                    if ($value === '') {
                        $value = null;
                    }
                }

                if ($columnNames) {
                    $cells[$columnNames[$cellColumn]] = trim($value);
                } else {
                    $cells[$columnLetter++] = trim($value);
                }

                if ($columnCount > 0) {
                    $columnCount--;

                    if ($columnCount === 0) {
                        break;
                    }
                }
            }

            if (array_filter($cells)) {
                $rows[] = $cells;
            } else {
                break;
            }
        }

        return $rows;
    }

    /**
     * @param Spreadsheet $objPHPExcel
     * @param $rows
     * @param int $activeSheetIndex
     * @param array $columnNames
     *
     * @return Spreadsheet
     * @throws Exception
     * @todo: указывать имена колонок, например ['A' => 'dsada', 'B' => 'sdad', 'D' => 'dsadas'], но не обязательно
     *
     */
    public static function setData(Spreadsheet $objPHPExcel, $rows, $activeSheetIndex = 0, array $columnNames = [])
    {
        $objPHPExcel->setActiveSheetIndex($activeSheetIndex);

        if ($columnNames) {
            $rows = array_map(function ($row) use ($columnNames) {
                return array_map(function ($columnName) use ($row) {
                    if (is_callable($columnName)) {
                        return $columnName($row);
                    }
                    return $row[$columnName];
                }, $columnNames);
            }, $rows);
        }

        $objPHPExcel
            ->getActiveSheet()
            ->fromArray($rows, null, 'A1');

        return $objPHPExcel;
    }

    public static function getDataFromFile(string $filePath, $ignoreFirstRow = 1, $columnNames = [])
    {
        return array_slice(Vendor_PHPExcel::getData(IOFactory::load($filePath), 0, $columnNames), (int) $ignoreFirstRow);
    }
}