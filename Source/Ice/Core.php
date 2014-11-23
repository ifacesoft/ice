<?php
/**
 * Ice common core trait
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

use Ice;
use Ice\Core\Code_Generator;
use Ice\Core\Config;
use Ice\Core\Data_Provider;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Resource;
use Ice\Data\Provider\Registry;
use Ice\Helper\Object;

/**
 * Trait Core
 *
 * Common static methods for containers or others
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 *
 * @version 0.0
 * @since 0.0
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
     * @since 0.0
     */
    public static function getShortName()
    {
        return Object::getShortName(self::getClass());
    }

    /**
     * Return class by base class
     *
     * @param null $className
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getClass($className = null)
    {
        return empty($className)
            ? get_called_class()
            : Object::getClass(get_called_class(), $className);
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
     * @since 0.0
     */
    public static function getModuleAlias()
    {
        return Object::getModuleAlias(self::getClass());
    }

    /**
     * Return instance of resource for self class
     *
     * @return Resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getResource()
    {
        return Resource::getInstance(self::getClass());
    }

    /**
     * Return config of self class
     *
     * @param array $config
     * @param null $postfix
     * @return Config
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getConfig(array $config = [], $postfix = null)
    {
        return Config::getInstance(self::getClass(), $config, $postfix);
    }

    /**
     * Return dat provider for self class
     *
     * @param null $postfix
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @since 0.0
     */
    public static function getClassName()
    {
        return Object::getName(self::getClass());
    }

    /**
     * Return code generator for self class type
     *
     * @return Code_Generator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getCodeGenerator()
    {
        return Code_Generator::getInstance(self::getClass());
    }

    /**
     * Return namespace by base class
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getNamespace()
    {
        return Object::getNamespace(self::getBaseClass(), self::getClass());
    }

    /**
     * Return base class for self class (class extends Container)
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getBaseClass()
    {
        return Object::getBaseClass(self::getClass());
    }

    /**
     * Create new instance of self class
     *
     * @param $key
     * @param $hash
     * @return null
     * @throws Core\Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($key, $hash = null)
    {
        Resource::getLogger()->fatal(['Implementation {$0} is required for {$1}', [__FUNCTION__, self::getClass()]], __FILE__, __LINE__);
        return null;
    }

    /**
     * Return logger for self class
     *
     * @return Logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getLogger()
    {
        return Logger::getInstance(self::getClass());
    }

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        Resource::getLogger()->fatal(['Implementation {$0} is required for {$1}', [__FUNCTION__, self::getClass()]], __FILE__, __LINE__);
        return null;
    }

    /**
     * Return registry storage for  class
     *
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getRegistry() {
        return Registry::getInstance(self::getClass());
    }
}