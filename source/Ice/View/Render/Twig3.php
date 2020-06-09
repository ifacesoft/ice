<?php
/**
 * Ice view render implementation twig class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Module;
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
 * @package    Ice
 * @subpackage View_Render
 *
 * @version 0.0
 * @since   0.0
 */
class Twig3 extends View_Render
{
    const TEMPLATE_EXTENTION = '.twig';

    protected $templateDirs = [];

    /**
     * Constructor of php view render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function __construct()
    {
//        $twigPath = VENDOR_DIR . $config->get('vendor') . '/lib/';
//
//        include_once $twigPath . 'Twig/Environment.php';
//        include_once $twigPath . 'Twig/Autoloader.php';
//
//        Loader::register('Twig_Autoloader::autoload');

        foreach (Module::getAll() as $module) {
            $this->templateDirs[] = $module->get(Module::RESOURCE_DIR);
        }
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        try {
            if ($templateType == View_Render::TEMPLATE_TYPE_STRING) {
                $twig = new \Twig\Environment(new \Twig_Loader_String(), $options);
                return $twig->render($template, $data);
            } else {
                $loader = new \Twig\Loader\FilesystemLoader($this->templateDirs);
                $twig = new \Twig\Environment(
                    $loader,
                    ['cache' => Module::getInstance()->get('cacheDir') . Twig::getConfig()->get('cache')]
                );
                return $twig->render(str_replace(['_', '\\'], '/', $template) . Twig::TEMPLATE_EXTENTION, $data);
            }
        } catch (\Exception $e) {
            if (Environment::getInstance()->isDevelopment()) {
                View::getLogger()->info(
                    [
                        Twig::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, Twig::getClassName()]
                    ],
                    Logger::WARNING
                );
                $twig = new \Twig\Environment(new \Twig_Loader_String());
                return $twig->render(Twig::getCodeGenerator()->generate($template), $data);
            } else {
                return View::getLogger()->error(
                    [Twig::getClassName() . ': View {$0} not found', $template],
                    __FILE__,
                    __LINE__,
                    $e
                );
            }
        }
    }
}
