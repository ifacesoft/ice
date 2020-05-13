<?php

namespace Ice\Render;

use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Core\Widget;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Ice\Code\Generator\Render_External_PHPWord as CodeGenerator_Render_PhpWord;
class External_PHPWord extends Render
{
    const TEMPLATE_EXTENSION = '.docx.php';

    private $word = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->word = new PhpWord();

//        $config = Config::getInstance(__CLASS__);
//
//        $fontStyleName = 'iceDefaultDefinedStyle';
//
//        $this->spreadsheet->addFontStyle($fontStyleName, $config->gets('font_style'));
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
     * @version 1.9
     * @since   1.9
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            return '';
        }

        $templateFilePath = Loader::getFilePath($template, External_PHPWord::TEMPLATE_EXTENSION, Module::RESOURCE_DIR, false, true);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        External_PHPWord::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, External_PHPWord::getClassName()]
                    ],
                    Logger::WARNING
                );

                return CodeGenerator_Render_PhpWord::getInstance($template)->generate();
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
        $widget->setRenderClass(__CLASS__);

        $widget->render($this);

        ob_start();

        $objWriter = IOFactory::createWriter($this->getWord(), 'Word2007');

        $objWriter->save('php://output');

        return ob_get_clean();
    }

    /**
     * @return PhpWord
     */
    public function getWord()
    {
        return $this->word;
    }

    public function getSection()
    {
        return $this->getWord()->addSection();
    }
}