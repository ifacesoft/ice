<?php
/**
 * Ice common core trait
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

use Ice\Core\Code_Generator;
use Ice\Core\Config;
use Ice\Core\Data_Provider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Resource;
use Ice\Data\Provider\Cacher;
use Ice\Data\Provider\Registry;
use Ice\Data\Provider\Repository;
use Ice\Helper\Object;

/**
 * Trait Core
 *
 * Common static methods for containers or others
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 */
trait Core
{
    /**
     * Return short name of class (Ice:Class_Name)
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getShortName()
    {
        return Object::getShortName(self::getClass());
    }

    /**
     * Return class by base class
     *
     * @param  string|null $className
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getClass($className = null)
    {
        return empty($className)
            ? get_called_class()
            : Object::getClass(get_called_class(), $className);
    }

    /**
     * Return config of self class
     *
     * @param  null $postfix
     * @param  bool $isRequired
     * @param  null $ttl
     * @return Config
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getConfig($postfix = null, $isRequired = false, $ttl = null)
    {
        return Config::getInstance(self::getClass(), $postfix, $isRequired, $ttl);
    }

    /**
     * Return dat provider for self class
     *
     * @param  string|null $postfix
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getDataProvider($postfix = null)
    {
        if (empty($postfix)) {
            $postfix = strtolower(self::getClassName());
        }

        return Environment::getInstance()->getProvider(self::getBaseClass(), $postfix);
    }

    /**
     * Return class name (without namespace)
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getClassName()
    {
        return Object::getClassName(self::getClass());
    }

    /**
     * Return base class for self class (class extends Container)
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getBaseClass()
    {
        return Object::getBaseClass(self::getClass());
    }

    /**
     * Return code generator for self class type
     *
     * @return Code_Generator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getCodeGenerator()
    {
        $baseClass = self::getBaseClass();

        $class = $baseClass == self::getClass()
            ? self::getModuleAlias() . ':' . self::getClassName()
            : self::getModuleAlias() . ':' . $baseClass::getClassName() . '_' . self::getClassName();

        return Code_Generator::getInstance($class);
    }

    /**
     * Get module name of object
     *
     * 'Ice/Model/Ice/User' => 'Ice'
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getModuleAlias()
    {
        return Object::getModuleAlias(self::getClass());
    }

    /**
     * Return namespace by base class
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getNamespace()
    {
        return Object::getNamespace(self::getBaseClass(), self::getClass());
    }

    /**
     * Return logger for self class
     *
     * @return Logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getLogger()
    {
        return Logger::getInstance(self::getClass());
    }

    /**
     * Return registry storage for class
     *
     * @param  string $index
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getRegistry($index = 'default')
    {
        return Registry::getInstance(self::getClass(), $index);
    }

    /**
     * Return repository storage for class
     *
     * @param  string $index
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.4
     */
    public static function getRepository($index = 'default')
    {
        return Repository::getInstance(self::getClass(), $index);
    }

    /**
     * Return cacher storage for class
     *
     * @param  string $index
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getCacher($index = 'default')
    {
        return Cacher::getInstance(self::getClass(), $index);
    }

    public function dumpDie()
    {
        Debuger::dumpDie($this);
        return $this;
    }

    public function dump()
    {
        Debuger::dump($this);
        return $this;
    }

    public function __toString()
    {
        return (string)get_class($this);
    }
}
