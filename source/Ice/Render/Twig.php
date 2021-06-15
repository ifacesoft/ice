<?php
/**
 * Ice view render implementation twig class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Twig\Loader\FilesystemLoader;
use Ice\Code\Generator\Render_Twig as CodeGenerator_Render_Twig;

/**
 * Class Twig
 *
 * Implementation view render twig template
 *
 * @see \Ice\Core\Render
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
    const TEMPLATE_EXTENSION = '.twig';

    protected $templateDirs = [];

    public function __construct(array $data)
    {
        parent::__construct($data);

        //        $twigPath = VENDOR_DIR . $config->get('vendor') . '/lib/';
//
//        include_once $twigPath . 'Twig/Environment.php';
//        include_once $twigPath . 'Twig/Autoloader.php';
//
//        Loader::register('Twig_Autoloader::autoload');

        foreach (Module::getAll() as $module) {
            $this->templateDirs[] = $module->getPath(Module::RESOURCE_DIR);
        }
    }

    /**
     * @param null $instanceKey
     * @param null $ttl
     * @param array $params
     * @return Twig|Render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    private function getTwigExtensionInstance($extension) {
        if (is_array($extension)) {
            list($class, $args) = array_pad($extension, 2, []);

            $extensionClass = new \ReflectionClass($class);

            return $extensionClass->newInstanceArgs((array) $args);
        }

        return new $extension();
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param array $data
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
            return $template;
        }

        $environment = Environment::getInstance();

        try {
            $config = Config::getInstance(__CLASS__);

            $options = [
                'debug' => !$environment->isProduction(),
            ];

            if (!$environment->isDevelopment()) {
                $options['cache'] = \getCacheDir() . $config->get('cache');
            }

            if ($templateType === Render::TEMPLATE_TYPE_STRING) {
                $twig = new \Twig\Environment(new \Twig_Loader_String(), $options);

                foreach ($config->gets('extensions/' . $environment->getName()) as $extension) {
                    $twig->addExtension($this->getTwigExtensionInstance($extension));
                }

                return $twig->render($template, $data);
            } else {
                $twig = new \Twig\Environment(new FilesystemLoader($this->templateDirs), $options);

                foreach ($config->gets('extensions/' . $environment->getName()) as $extensionClass) {
                    $twig->addExtension($this->getTwigExtensionInstance($extensionClass));
                }

                return $twig->render(str_replace(['_', '\\'], '/', $template) . Twig::TEMPLATE_EXTENSION, $data);
            }
        } catch (\Exception $e) {
            if ($environment->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        Twig::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, Twig::getClassName()]
                    ],
                    Logger::WARNING
                );
                try {
                    $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader());

                    return $twig->createTemplate(CodeGenerator_Render_Twig::getInstance($template)->generate())->render($data);
                } catch (\Exception $e1) {
                    return $this->getLogger()->error(
                        $e1->getMessage(),
                        __FILE__,
                        __LINE__,
                        $e,
                        ['stacktrace' => $e1->getTraceAsString()]
                    );
                }
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
