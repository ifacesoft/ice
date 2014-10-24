<?php
/**
 * Ice view render implementation twig class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Response;
use Ice\Core\View;
use Ice\Core\View_Render;

/**
 * Class Twig
 *
 * Implementation view render twig template
 *
 * @see Ice\Core\View_Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage View_Render
 *
 * @version stable_0
 * @since stable_0
 */
class Twig extends View_Render
{
    const TEMPLATE_EXTENTION = '.twig';

    /**
     * Twig file instance
     *
     * @var \Twig_Environment
     */
    private $_fileTwig = null;

    /**
     * Twig string insatance
     *
     * @var \Twig_Environment
     */
    private $_stringTwig = null;

    /**
     * Constructor of php view render
     *
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
        $twigPath = VENDOR_DIR . $config->get('vendor') . '/lib/';

        require_once $twigPath . 'Twig/Environment.php';
        require_once $twigPath . 'Twig/Autoloader.php';

        Loader::register('Twig_Autoloader::autoload');

        $templateDirs = [];

        foreach (Module::get() as $module) {
            $templateDirs[] = $module['path'] . 'Resource';
        }

        $loader = new \Twig_Loader_Filesystem($templateDirs);
        $this->_fileTwig = new \Twig_Environment($loader, ['cache' => CACHE_DIR . $config->get('cache')]);
        $this->_stringTwig = new \Twig_Environment(new \Twig_Loader_String());
    }

    /**
     * Display rendered view in standard output
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     */
    public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        Response::send($this->fetch($template, $data, $templateType));
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
//        $template = Action::getClass($template);

        try {
            return $templateType == View_Render::TEMPLATE_TYPE_STRING
                ? $this->_stringTwig->render($template, $data)
                : $this->_fileTwig->render(str_replace(['_', '\\'], '/', $template) . Twig::TEMPLATE_EXTENTION, $data);
        } catch (\Exception $e) {
            if (Environment::isDevelopment()) {
                View::getLogger()->info([Twig::getClassName() . ': View {$0} not found. Trying generate template {$1}...', [$template, Twig::getClassName()]], Logger::WARNING);

                return $this->_stringTwig->render(Twig::getCodeGenerator()->generate($template), $data);
            } else {
                return View::getLogger()->error([Smarty::getClassName() . ': View {$0} not found', $template], __FILE__, __LINE__, $e);
            }
        }
    }
}