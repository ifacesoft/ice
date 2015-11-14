<?php

namespace Ice\Render;

use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Core\Widget;
use PHPExcel_Shared_Font;
use PHPExcel_Writer_Excel2007;

class External_PHPExcel extends Render
{
    const TEMPLATE_EXTENTION = '.xlsx.php';

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $data)
    {
//        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param array $data
     * @param string|null $layout Emmet style layout
     * @param  string $templateType
     * @return string
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        $templateFilePath = Loader::getFilePath($template, External_PHPExcel::TEMPLATE_EXTENTION, Module::RESOURCE_DIR, false);

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

    public function renderWidget(Widget $widget) {
        ob_start();
        ob_implicit_flush(false);

        $xls = new \PHPExcel();
        $xls->setActiveSheetIndex(0);

        $widget->renderExternal(__CLASS__, ['sheet' => $xls->getActiveSheet(), 'column' => 'A', 'index' => 1]);

        foreach(range('A','G') as $columnID) {
            $xls->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

//        foreach(range(1, 100) as $rowID) {
//            $xls->getActiveSheet()->getRowDimension($rowID)->setRowHeight(-1);
//        }

        $objWriter = new PHPExcel_Writer_Excel2007($xls);

        $objWriter->save('php://output');

        return ob_get_clean();
    }
}