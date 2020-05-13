<?php
/**
 * Ice common core trait
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

require_once __DIR__ . '/../Ice/Helper/Class/Object.php';

use Ice\Core\Code_Generator;
use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\DataProvider\Cacher;
use Ice\DataProvider\Registry;
use Ice\DataProvider\Repository;
use Ice\Helper\Class_Object;

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
    private static $selfCache = [];

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
        return Class_Object::getShortName(self::getClass());
    }

    /**
     * Return class by base class
     *
     * @param  string|null $className
     * @param null $baseClass
     * @return Core|string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function getClass($className = null, $baseClass = null)
    {
        if (empty($className)) {
            return get_called_class();
        }

        if (!strpos($className, ':') && strpos($className, '\\')) {
            return $className;
        }

        if ($className[0] == '_' && $baseClass) {
            if (is_object($baseClass)) {
                $baseClass = get_class($baseClass);
            }

            if (strlen($className) > 1 && $className[1] == '_') {
                return substr($baseClass, 0, strrpos($baseClass, '_')) . substr($className, 1);
            } else {
                return $baseClass . $className;
            }
        } else {
            return Class_Object::getClass(get_called_class(), $className);
        }
    }

    /**
     * Return dat provider for self class
     *
     * @param  string|null $key
     * @return DataProvider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     * @deprecated 1.10
     * @version 0.0
     * @since   0.0
     */
    public static function getDataProvider($key)
    {
        return Environment::getInstance()->getProvider(self::getClass(), $key);
    }

    /**
     * Return code generator for self class type
     *
     * @param $class
     * @return Code_Generator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated;
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getCodeGenerator($class)
    {
        $baseClass = self::getBaseClass();

        $codeGeneratorClass = $baseClass == self::getClass()
            ? $baseClass
            : $baseClass . '_' . self::getClassName();

        return Code_Generator::getInstance($codeGeneratorClass . '/' . $class);
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
        return Class_Object::getBaseClass(self::getClass());
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
        return Class_Object::getClassName(self::getClass());
    }

    public static function getClassNamespace() {
        return substr(static::class, 0, strrpos(static::class, '\\'));
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
        return Class_Object::getModuleAlias(self::getClass());
    }

    public static function getModuleNamespace()
    {
        return Class_Object::getNamespace(self::getBaseClass(), self::getClass());
    }

    /**
     * Return registry storage for class
     *
     * @param  string $index
     * @return Registry|DataProvider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     * @throws Core\Exception
     */
    public static function getRegistry($index = 'default')
    {
        return Registry::getInstance(self::getClass(), $index);
    }

    /**
     * Return repository storage for class
     *
     * @param  string $index
     * @return Repository|DataProvider
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
     * @return Cacher|DataProvider
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
}
