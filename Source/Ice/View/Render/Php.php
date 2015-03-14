<?php
/**
 * Ice view render implementation php class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\Action;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\View;
use Ice\Core\View_Render;

/**
 * Class Php
 *
 * Implementation view render php template
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
class Php extends View_Render
{
    const TEMPLATE_EXTENTION = '.tpl.php';

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
    }
//
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
//        $templateFilePath = Loader::getFilePath($template, self::TEMPLATE_EXTENTION, 'Resource/', false);
//
//        if (!file_exists($templateFilePath)) {
//            if (Environment::getInstance()->isDevelopment()) {
//                View::getLogger()->info([Php::getClassName() . ': View {$0} not found. Trying generate template {$1}...', [$template, Php::getClassName()]], Logger::WARNING);
//
//                echo Php::getCodeGenerator()->generate($template);
//            } else {
//                echo View::getLogger()->error(['Render error in template "{$0}" "{$1}"', [$templateFilePath, ob_get_clean()]], __FILE__, __LINE__);
//            }
//        }
//
//        extract($data);
//        unset($data);
//
//        require $templateFilePath;
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
        $templateFilePath = Loader::getFilePath($template, Php::TEMPLATE_EXTENTION, 'Resource/', false);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                View::getLogger()->info([Php::getClassName() . ': View {$0} not found. Trying generate template {$1}...', [$template, Php::getClassName()]], Logger::WARNING);

                return Php::getCodeGenerator()->generate($template);
            } else {
                return View::getLogger()->error(['Render error in template "{$0}" "{$1}"', [$templateFilePath, ob_get_clean()]], __FILE__, __LINE__);
            }
        }

        ob_start();
        ob_implicit_flush(false);

        extract($data);
        unset($data);

        require $templateFilePath;
        return ob_get_clean();
    }

    /**
     * Return php view render
     *
     * @param mixed $key
     * @param int $ttl
     * @return Php
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}