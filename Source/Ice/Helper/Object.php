<?php
/**
 * Ice helper object class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Container;
use Ice\Core\Module;

/**
 * Class object
 *
 * Helper for objects and classes
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version stable_0
 * @since stable_0
 */
class Object
{
    /**
     * Return namespace by base class
     *
     * @param $baseClass
     * @param $name
     * @return string
     */
    public static function getNamespace($baseClass, $name)
    {
        $class = self::getClass($baseClass, $name);
        return strstr($class, Object::getName($class), true);
    }

    /**
     * Return class by base class
     *
     * @param $baseClass
     * @param $name
     * @return string
     */
    public static function getClass($baseClass, $name)
    {
        if (!self::isShortName($name)) {
            return $name;
        }

        list($moduleAlias, $objectName) = explode(':', $name);

        return $moduleAlias . '\\' . str_replace('_', '\\', Object::getName($baseClass)) . '\\' . $objectName;
    }

    /**
     * Check is short name (Ice:Class_Name)
     *
     * @param $shortName
     * @return bool
     */
    private static function isShortName($shortName)
    {
        return (bool)strpos($shortName, ':');
    }

    /**
     * Return class name (without namespace)
     *
     * @param $class
     * @return string
     */
    public static function getName($class)
    {
        if (!self::isClass($class)) {
            return $class;
        }

        return substr($class, strrpos($class, '\\') + 1);
    }

    /**
     * Check is class with namespace (?)
     *
     * @param $class
     * @return bool
     */
    private static function isClass($class)
    {
        return (bool)strpos(ltrim($class, '\\'), '\\');
    }

    /**
     * Return alias of namespace class
     *
     * @param $class
     * @param $shortName
     * @return string
     */
    public static function getPrefixByClassShortName($class, $shortName)
    {
        return self::getModuleAlias(self::getClass($class, $shortName));
    }

    /**
     * Get module name of object
     *
     * 'Ice/Model/Ice/User' => 'Ice'
     *
     * @param $class
     * @return string
     */
    public static function getModuleAlias($class)
    {
        $pos = strpos(ltrim($class, '\\'), '\\');
        return $pos ? substr($class, 0, $pos) : Module::getInstance()->getAlias();
    }

    /**
     * Return short name of class (Ice:Class_Name)
     *
     * @param $class
     * @return string
     */
    public static function getShortName($class)
    {
        return self::getModuleAlias($class) . ':' . self::getName($class);
    }

    /**
     * Return base class (class extends Container)
     *
     * @param $class
     * @return mixed
     */
    public static function getBaseClass($class)
    {
        foreach (class_parents($class) as $parentClass) {
            if ($parentClass == Container::getClass()) {
                break;
            }

            $class = $parentClass;
        }

        return $class;
    }
} 