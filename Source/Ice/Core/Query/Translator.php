<?php
/**
 * Ice core query translator class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;

/**
 * Class Query_Translator
 *
 * Core query translator abstract class
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
abstract class Query_Translator extends Container
{
    use Core;

    /**
     * Private constructor for query translator
     */
    private function __construct()
    {
    }

    /**
     * Create new instance of query translator
     *
     * @param $key
     * @return Query_Translator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function create($key)
    {
        $queryTranslatorClass = self::getClass();
        return new $queryTranslatorClass();
    }

    /**
     * Translate query parts to sql string
     *
     * @param array $sqlParts
     * @return string
     * @throws Exception
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function translate(array $sqlParts);
}