<?php

namespace Ice\Render;

use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Core\Widget;
use Ice\Helper\Type_Char;
use Ice\Code\Generator\Render_External_PHPExcel as CodeGenerator_Render_PhpExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class External_PHPExcel extends Render
{
    const TEMPLATE_EXTENSION = '.xlsx.php';

    private $spreadsheet = null;

    private $sheets = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        //        Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $this->spreadsheet = new Spreadsheet();

        $config = Config::getInstance(__CLASS__);

        $this->spreadsheet->getDefaultStyle()->getFont()->setName($config->get('font_name'));
        $this->spreadsheet->getDefaultStyle()->getFont()->setSize($config->get('font_size'));
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
            return '';
        }

        $templateFilePath = Loader::getFilePath($template, External_PHPExcel::TEMPLATE_EXTENSION, Module::RESOURCE_DIR, false, true);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        External_PHPExcel::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, External_PHPExcel::getClassName()]
                    ],
                    Logger::WARNING
                );

                return CodeGenerator_Render_PhpExcel::getInstance($template)->generate();
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
        $this->getSpreadsheet()->setActiveSheetIndex(0);

        $widget->setRenderClass(__CLASS__);

        $widget->render($this);

//        foreach (range('A', 'K') as $columnID) {
//            $this->getPHPExcel()->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
//        }

//        foreach(range(1, 100) as $rowID) {
//            $xls->getActiveSheet()->getRowDimension($rowID)->setRowHeight(-1);
//        }

        if ($widget->getOption('landscapeAutofit', false)) {
            $this->getSpreadsheet()->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $this->getSpreadsheet()->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $this->getSpreadsheet()->getActiveSheet()->getPageSetup()->setFitToHeight(0);
        }

        ob_start();

        $objWriter = new Xlsx($this->getSpreadsheet());

        $objWriter->save('php://output');

        return ob_get_clean();
    }

    public function getSpreadsheet()
    {
        return $this->spreadsheet;
    }

    public function indexInc($step = 1, $activeSheetIndex = null)
    {
        $index = $this->getIndex() + $step;

        $this->setIndex($index, $activeSheetIndex);
        $this->setColumn('A', $activeSheetIndex);

        return $index;
    }

    /**
     * @param null $activeSheetIndex
     * @return null
     */
    public function getIndex($activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getSpreadsheet()->getActiveSheetIndex();
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
            $activeSheetIndex = $this->getSpreadsheet()->getActiveSheetIndex();
        }

        $this->sheets[$activeSheetIndex]['index'] = $index;
    }

    /**
     * @param null $column
     * @param null $activeSheetIndex
     */
    public function setColumn($column, $activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getSpreadsheet()->getActiveSheetIndex();
        }

        $this->sheets[$activeSheetIndex]['column'] = $column;
    }

    public function columnInc($step = 1, $activeSheetIndex = null)
    {
        for ($i = 0, $column = $this->getColumn(); $i < $step; $i++, $column++) ;

        $this->setColumn($column, $activeSheetIndex);

        return $column;
    }

    /**
     * @param null $activeSheetIndex
     * @return null
     */
    public function getColumn($activeSheetIndex = null)
    {
        if ($activeSheetIndex === null) {
            $activeSheetIndex = $this->getSpreadsheet()->getActiveSheetIndex();
        }

        if (!isset($this->sheets[$activeSheetIndex])) {
            $this->sheets[$activeSheetIndex] = [];
            $this->setIndex(1, $activeSheetIndex);
            $this->setColumn('A', $activeSheetIndex);
        }

        return $this->sheets[$activeSheetIndex]['column'];
    }

    public function columDec($activeSheetIndex = null)
    {
        $column = $this->decrementLetter($this->getColumn());

        $this->setColumn($column, $activeSheetIndex);

        return $column;
    }

    public function decrementLetter($char)
    {
        if ($char == 'A' || $char == 'a') {
            return $char;
        }

        $z = Type_Char::isUpperCase($char) ? 'Z' : 'z';

        $len = strlen($char);

        // last character is A or a
        if (ord($char[$len - 1]) === 65 || ord($char[$len - 1]) === 97) {
            if ($len === 1) { // one character left
                return '';
            } else if ($char == 'AA' || $char == 'aa') {
                return $z;
            } else { // 'ABA'--;  => 'AAZ'; recursive call
                return $this->decrementLetter(substr($char, 0, -1)) . $z;
                // $char = $this->decrementLetter(substr($char, 0, -1)) . $z;
            }
        } else {
            $char[$len - 1] = chr(ord($char[$len - 1]) - 1);
        }
        return $char;
    }

    public function getSheet()
    {
        return $this->getSpreadsheet()->getActiveSheet();
    }
}