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
 * @version 0.0
 * @since 0.0
 */
abstract class View_Render extends Container
{
    use Stored;

    const DEFAULT_KEY = 'instance';
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
     * @param $key
     * @return View_Render
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function create($key)
    {
        $viewRenderClass = self::getClass();
        return new $viewRenderClass();
    }

    /**
     * Return current processing template
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getLastTemplate()
    {
        if (empty(self::$templates)) {
            return '';
        }

        return reset(View_Render::$templates);
    }

//    /**
//     * Display rendered view in standard output
//     *
//     * @param $template
//     * @param array $data
//     * @param string $templateType
//     *
//     * @author anonymous <email>
//     *
//     * @version 0
//     * @since 0
//     */
//    abstract public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE);

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE);

    /**
     * Default action key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return View_Render::DEFAULT_KEY;
    }

    /**
     * Default class key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultClassKey()
    {
        return self::getClass() . '/default';
    }
}