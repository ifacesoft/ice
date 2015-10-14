<?php
/**
 * Ice view render implementation twig class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\ViiewOld;
use Ice\Core\Render;

/**
 * Class Twig
 *
 * Implementation view render twig template
 *
 * @see Ice\Core\Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Render
 *
 * @version 0.0
 * @since   0.0
 */
class Twig extends Render
{
    const TEMPLATE_EXTENTION = '.twig';

    protected $templateDirs = [];

    protected function init(array $data)
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
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Twig
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param  array $data
     * @param null $layout
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        try {
            if ($templateType == Render::TEMPLATE_TYPE_STRING) {
                $twig = new \Twig_Environment(new \Twig_Loader_String());
                return $twig->render($template, $data);
            } else {
                $loader = new \Twig_Loader_Filesystem($this->templateDirs);
                $twig = new \Twig_Environment(
                    $loader,
                    ['cache' => Module::getInstance()->get('cacheDir') . Config::getInstance(__CLASS__)->get('cache')]
                );
                return $twig->render(str_replace(['_', '\\'], '/', $template) . Twig::TEMPLATE_EXTENTION, $data);
            }
        } catch (\Exception $e) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        Twig::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, Twig::getClassName()]
                    ],
                    Logger::WARNING
                );
                $twig = new \Twig_Environment(new \Twig_Loader_String());
                return $twig->render(Twig::getCodeGenerator($template)->generate(), $data);
            } else {
                return $this->getLogger()->error(
                    [Twig::getClassName() . ': View {$0} not found', $template],
                    __FILE__,
                    __LINE__,
                    $e
                );
            }
        }
    }
}
