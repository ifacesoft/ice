<?php
/**
 * Ice view render implementation smarty class
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
    private $_smarty = null;


    protected function init(array $params)
    {
        $config = Config::getInstance(__CLASS__);

//        include_once VENDOR_DIR . $config->get('vendor') . '/libs/Smarty.class.php';

        $this->_smarty = new \Smarty();

//        Loader::register('\smartyAutoload');

        $templateDirs = [];

        foreach (Module::getAll() as $module) {
            $templateDirs[] = $module->get(Module::RESOURCE_DIR);
        }

        $this->_smarty->setTemplateDir($templateDirs);
        $this->_smarty->setCompileDir(Module::getInstance()->get('cacheDir') . $config->get('templates_c'));
        $this->_smarty->addPluginsDir($config->gets('plugins', false));
        //        $this->_smarty->setCacheDir('/web/www.example.com/smarty/cache');
        //        $this->_smarty->setConfigDir('/web/www.example.com/smarty/configs');
        $this->_smarty->debugging = false;
    }

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Smarty
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
     * @param $template
     * @param  array $data
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        /**
         * @var \Smarty_Internal_Template $smartyTemplate
         */
        $smartyTemplate = $templateType == Render::TEMPLATE_TYPE_STRING
            ? $this->_smarty->createTemplate('string:' . $template)
            : $this->_smarty->createTemplate(str_replace(['_', '\\'], '/', $template) . Smarty::TEMPLATE_EXTENTION);

        foreach ($data as $key => $value) {
            $smartyTemplate->assign($key, $value);
        }

        try {
            return $smartyTemplate->fetch();
        } catch (\Exception $e) {
            if (Environment::getInstance()->isDevelopment()) {
                ViiewOld::getLogger()->info(
                    ['View {$0} not found. Trying generate template {$1}...', [$template, Smarty::getClassName()]],
                    Logger::WARNING
                );

                /**
                 * @var \Smarty_Internal_Template $smartyTemplate
                 */
                $smartyTemplate = $this->_smarty->createTemplate(
                    'string:' . Smarty::getCodeGenerator()->generate($template)
                );

                foreach ($data as $key => $value) {
                    $smartyTemplate->assign($key, $value);
                }

                return $smartyTemplate->fetch();
            } else {
                return ViiewOld::getLogger()->error(
                    [Smarty::getClassName() . ': View {$0} not found', $template],
                    __FILE__,
                    __LINE__,
                    $e
                );
            }
        }
    }
}
