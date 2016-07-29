<?php
/**
 * Ice view render implementation smarty class
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
use Ice\Core\ViiewOld;
use Ice\Helper\Emmet;

/**
 * Class Smarty
 *
 * Implementation view render smarty template
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
class Smarty extends Render
{
    const TEMPLATE_EXTENTION = '.tpl';

    /**
     * Smarty instance
     *
     * @var /Smarty
     */
    private $smarty = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $config = Config::getInstance(__CLASS__);

//        include_once VENDOR_DIR . $config->get('vendor') . '/libs/Smarty.class.php';

        $this->smarty = new \Smarty();

//        Loader::register('\smartyAutoload');

        $templateDirs = [];

        foreach (Module::getAll() as $module) {
            $templateDirs[] = $module->getPath(Module::RESOURCE_DIR);
        }

        $this->smarty->setTemplateDir($templateDirs);
        $this->smarty->setCompileDir(getCacheDir() . $config->get('templates_c'));
        $this->smarty->addPluginsDir($config->gets('plugins', []));
        //        $this->_smarty->setCacheDir('/web/www.example.com/smarty/cache');
        //        $this->_smarty->setConfigDir('/web/www.example.com/smarty/configs');
        $this->smarty->debugging = false;
    }

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Smarty
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
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
     * @version 1.1
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        /**
         * @var \Smarty_Internal_Template $smartyTemplate
         */
        $smartyTemplate = $templateType == Render::TEMPLATE_TYPE_STRING
            ? $this->smarty->createTemplate('string:' . $template)
            : $this->smarty->createTemplate(str_replace(['_', '\\'], '/', $template) . Smarty::TEMPLATE_EXTENTION);

        foreach ($data as $key => $value) {
            $smartyTemplate->assign($key, $value);
        }

        try {
            return $layout
                ? Emmet::translate($layout . '{{$content}}', ['content' => $smartyTemplate->fetch()])
                : $smartyTemplate->fetch();
        } catch (\Exception $e) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    ['View {$0} not found. Trying generate template {$1}...', [$template, Smarty::getClassName()]],
                    Logger::WARNING
                );

                /**
                 * @var \Smarty_Internal_Template $smartyTemplate
                 */
                $smartyTemplate = $this->smarty->createTemplate(
                    'string:' . Smarty::getCodeGenerator($template)->generate()
                );

                foreach ($data as $key => $value) {
                    $smartyTemplate->assign($key, $value);
                }

                return $smartyTemplate->fetch();
            } else {
                return $this->getLogger()->error(
                    [Smarty::getClassName() . ': View {$0} not found', $template],
                    __FILE__,
                    __LINE__,
                    $e
                );
            }
        }
    }
}
