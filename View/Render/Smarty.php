<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 11.01.14
 * Time: 0:41
 */

namespace ice\view\render;

use ice\core\Config;
use ice\core\Data_Provider;
use ice\core\Loader;
use ice\core\Logger;
use ice\core\View_Render;
use ice\Ice;

class Smarty extends View_Render
{
    const VIEW_RENDER_SMARTY_CLASS = 'ice\view\render\Smarty';
    const TEMPLATE_EXTENTION = '.tpl';

    public static $config = array(
        Data_Provider::KEY => 'Buffer:view_render/smarty'
    );

    /** @var \Smarty */
    private $_smarty = null;

    public function init()
    {
        require_once('/usr/local/share/smarty3/Smarty.class.php');
        $this->_smarty = new \Smarty();

        Loader::register('\smartyAutoload');

        $templateDirs = array();

        $modulesConfigName = Ice::getConfig()->getConfigName() . ':modules';

        foreach (Ice::getConfig()->getParams('modules') as $module) {
            $moduleConfig = new Config($module, $modulesConfigName);
            $templateDirs[] = $moduleConfig->getParam('path') . 'View/Template';
        }
        $this->_smarty->setTemplateDir($templateDirs);

        $this->_smarty->setCompileDir(dirname(Ice::getEnginePath()) . '/cache/' . Ice::getProject());
//        $this->_smarty->setCacheDir('/web/www.example.com/smarty/cache');
//        $this->_smarty->setConfigDir('/web/www.example.com/smarty/configs');
        $this->_smarty->debugging = true;
    }

    public function display($template, array $data = array(), $ext)
    {
        /** @var \Smarty_Internal_Template $smartyTemplate */
        $smartyTemplate = $this->_smarty->createTemplate($template . $ext);

        foreach ($data as $key => $value) {
            $smartyTemplate->assign($key, $value);
        }

        $smartyTemplate->display();
    }

    public function fetch($template, array $data = array(), $ext)
    {
        $templateName = $template . $ext;

        /** @var \Smarty_Internal_Template $smartyTemplate */
        $smartyTemplate = $this->_smarty->createTemplate($templateName);

        foreach ($data as $key => $value) {
            $smartyTemplate->assign($key, $value);
        }

        $view = null;

        try {
            $view = $smartyTemplate->fetch();
        } catch (\Exception $e) {
            ob_start();
            Logger::outputErrors($e);
            $view = ob_get_clean();
        }

        return $view;
    }
}