<?php
/**
 * Ice view render implementation smarty class
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
use Ice\Core\View;
use Ice\Core\View_Render;

/**
 * Class Smarty
 *
 * Implementation view render smarty template
 *
 * @see Ice\Core\View_Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage View_Render
 *
 * @version 0.0
 * @since 0.0
 */
class Smarty extends View_Render
{
    const TEMPLATE_EXTENTION = '.tpl';

    /**
     * Smarty instance
     *
     * @var /Smarty
     */
    private $_smarty = null;

    /**
     * Constructor of php view render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct()
    {
        $config = Smarty::getConfig();

        require_once VENDOR_DIR . $config->get('vendor') . '/libs/Smarty.class.php';

        $this->_smarty = new \Smarty();

        Loader::register('\smartyAutoload');

        $templateDirs = [];

        foreach (Module::get() as $module) {
            $templateDirs[] = $module['path'] . 'Resource';
        }

        $this->_smarty->setTemplateDir($templateDirs);
        $this->_smarty->setCompileDir(CACHE_DIR . $config->get('templates_c'));
        $this->_smarty->addPluginsDir($config->gets('plugins', false));
//        $this->_smarty->setCacheDir('/web/www.example.com/smarty/cache');
//        $this->_smarty->setConfigDir('/web/www.example.com/smarty/configs');
        $this->_smarty->debugging = true;
    }

//    /**
//     * Display rendered view in standard output
//     *
//     * @param $template
//     * @param array $data
//     * @param string $templateType
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
//    {
//        if ($templateType == View_Render::TEMPLATE_TYPE_STRING) {
//            $template = 'string:' . $template;
//        }
//
//        /** @var \Smarty_Internal_Template $smartyTemplate */
//        $smartyTemplate = $this->_smarty->createTemplate($template . self::TEMPLATE_EXTENTION);
//
//        foreach ($data as $key => $value) {
//            $smartyTemplate->assign($key, $value);
//        }
//
//        try {
//            $smartyTemplate->display();
//        } catch (\Exception $e) {
//            if (Environment::isDevelopment()) {
//                View::getLogger()->info([Smarty::getClassName() . ': View {$0} not found. Trying generate template {$1}...', [$template, Smarty::getClassName()]], Logger::WARNING);
//
//                /** @var \Smarty_Internal_Template $smartyTemplate */
//                $smartyTemplate = $this->_smarty->createTemplate('string:' . Smarty::getCodeGenerator()->generate($template));
//
//                foreach ($data as $key => $value) {
//                    $smartyTemplate->assign($key, $value);
//                }
//
//                $smartyTemplate->display();
//            } else {
//                Response::send(View::getLogger()->error([Smarty::getClassName() . ': View {$0} not found', $template], __FILE__, __LINE__, $e));
//            }
//        }
//    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
//        $template = Action::getClass($template);

        /** @var \Smarty_Internal_Template $smartyTemplate */
        $smartyTemplate = $templateType == View_Render::TEMPLATE_TYPE_STRING
            ? $this->_smarty->createTemplate('string:' . $template)
            : $this->_smarty->createTemplate(str_replace(['_', '\\'], '/', $template) . Smarty::TEMPLATE_EXTENTION);

        foreach ($data as $key => $value) {
            $smartyTemplate->assign($key, $value);
        }

        try {
            return $smartyTemplate->fetch();
        } catch (\Exception $e) {
            if (Environment::isDevelopment()) {
                View::getLogger()->info(Smarty::getClassName() . ': ' . $e->getMessage(), Logger::WARNING);
                View::getLogger()->info(['View {$0} not found. Trying generate template {$1}...', [$template, Smarty::getClassName()]], Logger::WARNING);

                /** @var \Smarty_Internal_Template $smartyTemplate */
                $smartyTemplate = $this->_smarty->createTemplate('string:' . Smarty::getCodeGenerator()->generate($template));

                foreach ($data as $key => $value) {
                    $smartyTemplate->assign($key, $value);
                }

                return $smartyTemplate->fetch();
            } else {
                return View::getLogger()->error([Smarty::getClassName() . ': View {$0} not found', $template], __FILE__, __LINE__, $e);
            }
        }
    }
}