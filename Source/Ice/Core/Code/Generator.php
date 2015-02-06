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
 * @version 0.0
 * @since 0.0
 */
abstract class Code_Generator extends Container
{
    use Core;

    /**
     * Create instance of code generator
     *
     * @param string $key Class of generated object
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($key)
    {
        /** @var Core $key */
        $baseClass = $key::getBaseClass();

        $className = $baseClass == $key
            ? $key::getClassName()
            : $baseClass::getClassName() . '_' . $key::getClassName();

        $key = 'Ice\Code\Generator\\' . $className;

        return new $key();
    }

    /**
     * Generate code and other
     *
     * @param array $data Sended data requered for generate
     * @param bool $force Force if already generate
     * @return string
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function generate($data, $force = false);
} 