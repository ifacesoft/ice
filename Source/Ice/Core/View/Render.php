<?php
/**
 * Ice core view render abstarct class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;

/**
 * Class View_Render
 *
 * Core view render abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
abstract class View_Render extends Container
{
    const TEMPLATE_TYPE_FILE = 'file';
    const TEMPLATE_TYPE_STRING = 'string';

    /**
     * Template stack in processing
     *
     * @var array
     */
    public static $templates = [];

    /**
     * Create new instance of view render
     *
     * @param View_Render $viewRenderClass
     * @param $hash
     * @return View_Render
     */
    protected static function create($viewRenderClass, $hash = null)
    {
        return new $viewRenderClass($viewRenderClass::getConfig());
    }

    /**
     * Return current processing template
     *
     * @return string
     */
    public static function getLastTemplate()
    {
        if (empty(self::$templates)) {
            return '';
        }

        return reset(View_Render::$templates);
    }

    /**
     * Return default view render key
     *
     * @return View_Render
     */
    protected static function getDefaultKey()
    {
        return self::getClass();
    }

    /**
     * Display rendered view in standard output
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     */
    abstract public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE);

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     */
    abstract public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE);
}