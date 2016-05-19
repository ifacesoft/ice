<?php

namespace Ice\Render;

use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Core\Widget;
use PHPExcel_Writer_Excel2007;

class External_PHPExcel extends Render
{
    const TEMPLATE_EXTENTION = '.xlsx.php';

    private $xls = null;

    private $sheets = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        //        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $this->xls = new \PHPExcel();
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param array $data
     * @param string|null $layout Emmet style layout
     * @param string $templateType
     * @return string
     * @throws \Exception
     * @author anonymous <email>
     *
     * @version 1.1
     * @since   1.1
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        $templateFilePath = Loader::getFilePath($template, External_PHPExcel::TEMPLATE_EXTENTION, Module::RESOURCE_DIR, false, true);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        External_PHPExcel::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, External_PHPExcel::getClassName()]
                    ],
                    Logger::WARNING
                );

                return External_PHPExcel::getCodeGenerator($template)->generate();
            } else {
                return $this->getLogger()->error(
                    ['Render error in template "{$0}" "{$1}"', [$templateFilePath, ob_get_clean()]],
                    __FILE__,
                    __LINE__
                );
            }
        }

        extract($data);
        unset($data);

        include $templateFilePath;

        return '';
    }

    public function renderWidget(Widget $widget)
    {
        $this->getPHPExcel()->setActiveSheetIndex(0);

        $widget->setRenderClass(__CLASS__);

        $widget->render($this);

        foreach (range('A', 'K') as $columnID) {
            $this->getPHPExcel()->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

//        foreach(range(1, 100) as $rowID) {
//            $xls->getActiveSheet()->getRowDimension($rowID)->setRowHeight(-1);
//        }

        ob_start();
        ob_implicit_flush(false);

        $objWriter = new PHPExcel_Writer_Excel2007($this->getPHPExcel());

        $objWriter->save('php://output');

        return ob_get_clean();
    }

    public function getPHPExcel()
    {
        return $this->xls;
    }

    /**
     * @param null $activeSheetIndex
     * @return null
     */
    public function getIndex($activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getPHPExcel()->getActiveSheetIndex();
        }

        if (!isset($this->sheets[$activeSheetIndex])) {
            $this->sheets[$activeSheetIndex] = [];
            $this->setIndex(1, $activeSheetIndex);
            $this->setColumn('A', $activeSheetIndex);
        }

        return $this->sheets[$activeSheetIndex]['index'];
    }

    /**
     * @param null $index
     * @param null $activeSheetIndex
     */
    public function setIndex($index, $activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getPHPExcel()->getActiveSheetIndex();
        }

        $this->sheets[$activeSheetIndex]['index'] = $index;
    }

    /**
     * @param null $activeSheetIndex
     * @return null
     */
    public function getColumn($activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getPHPExcel()->getActiveSheetIndex();
        }

        if (!isset($this->sheets[$activeSheetIndex])) {
            $this->sheets[$activeSheetIndex] = [];
            $this->setIndex(1, $activeSheetIndex);
            $this->setColumn('A', $activeSheetIndex);
        }

        return $this->sheets[$activeSheetIndex]['column'];
    }

    /**
     * @param null $column
     * @param null $activeSheetIndex
     */
    public function setColumn($column, $activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getPHPExcel()->getActiveSheetIndex();
        }

        $this->sheets[$activeSheetIndex]['column'] = $column;
    }

    public function indexInc($step = 1, $activeSheetIndex = null)
    {
        $index = $this->getIndex() + $step;
        
        $this->setIndex($index, $activeSheetIndex);
        $this->setColumn('A', $activeSheetIndex);
        
        return $index;
    }

    public function columnInc($step = 1, $activeSheetIndex = null)
    {
        for ($i = 0, $column = $this->getColumn(); $i < $step; $i++, $column++);
        
        $this->setColumn($column, $activeSheetIndex);
        
        return $column;
    }

    public function getSheet()
    {
        return $this->getPHPExcel()->getActiveSheet();
    }
}