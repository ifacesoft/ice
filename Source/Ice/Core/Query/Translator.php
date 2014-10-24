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
use Ice\Helper\Object;

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
 * @version stable_0
 * @since stable_0
 */
abstract class Query_Translator extends Container
{
    /**
     * Create new instance of query translator
     *
     * @param $class
     * @param null $hash
     * @return Query_Translator
     */
    protected static function create($class, $hash = null)
    {
        $moduleAlias = Object::getModuleAlias($class);
        $baseName = Query_Translator::getClassName();

        $class = $moduleAlias . '\\' . str_replace('_', '\\', $baseName) . '\\' . Object::getName($class);
        return new $class();
    }

    /**
     * Translate query parts to sql string
     *
     * @param array $sqlParts
     * @return string
     * @throws Exception
     */
    abstract public function translate(array $sqlParts);
}