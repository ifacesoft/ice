<?php
/**
 * Ice core code generator abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

/**
 * Class Code_Generator
 *
 * Core code generator container abstract class
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
abstract class Code_Generator extends Container
{
    /**
     * Create instance of code generator
     *
     * @param string $class Class of generated object
     * @param string $hash generated md5 hash
     * @return mixed
     */
    protected static function create($class, $hash = null)
    {
        /** @var Core $class */
        $baseClass = $class::getBaseClass();

        $className = $baseClass == $class
            ? $class::getClassName()
            : $baseClass::getClassName() . '_' . $class::getClassName();

        $class = 'Ice\Code\Generator\\' . $className;

        return new $class();
    }

    /**
     * Generate code and other
     *
     * @param array $data Sended data requered for generate
     * @param bool $force Force if already generate
     * @return string
     */
    abstract public function generate($data, $force = false);
} 