<?php

namespace Ice\View;

use Ice\Core\View;
use PHPExcel_Writer_Excel2007;

class Excel extends View
{
    private $column = 'A';
    private $row = 1;
    private $activeSheetIndex = 0;

    public function __construct()
    {
        $this->setRaw(new \PHPExcel());
    }

    public function getSheet($sheetIndex = null) {
        return $this->getRaw()->getSheet($sheetIndex === null ? $this->getActiveSheetIndex() : $sheetIndex);
    }

    public function getContent()
    {
        $sheet = $this->getSheet();


        foreach(range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

//        foreach(range(1, 100) as $rowID) {
//            $sheet->getRowDimension($rowID)->setRowHeight(-1);
//        }

        $objWriter = new PHPExcel_Writer_Excel2007($this->getRaw());

        ob_start();
        ob_implicit_flush(false);

        $objWriter->save('php://output');

        return ob_get_clean();
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

    public function incColumn($step = 1) {
        for ($i = 0; $i < $step; $i++) {
            $this->column++;
        }
    }

    public function incRow($step = 1) {
        for ($i = 0; $i < $step; $i++) {
            $this->row++;
        }
    }

    /**
     * @return \PHPExcel
     */
    public function getRaw()
    {
        return parent::getRaw();
    }
}