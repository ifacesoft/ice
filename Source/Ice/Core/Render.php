<?php
/**
 * Ice core view render abstarct class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

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
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
abstract class Render extends Container
{
    use Stored;

    const TEMPLATE_TYPE_FILE = 'file';
    const TEMPLATE_TYPE_STRING = 'string';

    /**
     * Template stack in processing
     *
     * @var array
     */
    public static $templates = [];

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Render
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
     * Return current processing template
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getLastTemplate()
    {
        if (empty(self::$templates)) {
            return '';
        }

        return reset(Render::$templates);
    }

    /**
     * Default action key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Default class key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultClassKey()
    {
        return 'Ice:Php';
    }

    /**
     * Render view via current view render
     *
     * @param  $template
     * @param  array $data
     * @param  string $templateType
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function fetch($template, array $data = [], $templateType = Render::TEMPLATE_TYPE_FILE);
}
