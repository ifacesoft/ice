<?php

namespace Ice\View;

use Ice\Core\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel extends View
{
    private $column = 'A';
    private $row = 1;
    private $activeSheetIndex = 0;

    public function __construct()
    {
        $this->setRaw(new Spreadsheet());
    }

    public function getContent()
    {
        $sheet = $this->getSheet();


        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

//        foreach(range(1, 100) as $rowID) {
//            $sheet->getRowDimension($rowID)->setRowHeight(-1);
//        }

        $objWriter = new Xlsx($this->getRaw());

        ob_start();

        $objWriter->save('php://output');

        return ob_get_clean();
    }

    public function getSheet($sheetIndex = null)
    {
        return $this->getRaw()->getSheet($sheetIndex === null ? $this->getActiveSheetIndex() : $sheetIndex);
    }

    /**
     * @return \PHPExcel
     */
    public function getRaw()
    {
        return parent::getRaw();
    }

    /**
     * @return int
     */
    public function getActiveSheetIndex()
    {
        return $this->activeSheetIndex;
    }

    /**
     * @param int $activeSheetIndex
     */
    public function setActiveSheetIndex($activeSheetIndex)
    {
        $this->activeSheetIndex = $activeSheetIndex;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    public function incColumn($step = 1)
    {
        for ($i = 0; $i < $step; $i++) {
            $this->column++;
        }
    }

    public function incRow($step = 1)
    {
        for ($i = 0; $i < $step; $i++) {
            $this->row++;
        }
    }
}