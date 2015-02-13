<?php
/**
 * Ice core container abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\File_Not_Found;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;

/**
 * Class Container
 *
 * Core container abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
abstract class Container
{
    /**
     * Return dat provider for self class
     *
     * @param null $postfix
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getDataProvider($postfix = null)
    {
        if (empty($postfix)) {
            $postfix = strtolower(Object::getName(self::getClass()));
        }

        return Environment::getInstance()->getProvider(self::getBaseClass(), $postfix);
    }

    public static function getClass($className = null)
    {
        return empty($className)
            ? get_called_class()
            : Object::getClass(get_called_class(), $className);
    }

    /**
     * Return base class for self class (class extends Container)
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getBaseClass()
    {
        return Object::getBaseClass(self::getClass());
    }

    /**
     * Get instance from container
     *
     * @param string $key
     * @param null $ttl
     * @throws Exception
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function getInstance($key = null, $ttl = null)
    {
        /** @var Container|Core $class */
        $class = self::getClass();

        /** @var Container|Core $baseClass */
        $baseClass = $class::getBaseClass();

        if ($class == $baseClass) {
            if (!$key) {
                return $baseClass::getInstance($class::getDefaultClassKey(), $ttl);
            } else if (is_string($key) && strpos($key, '/')) {
                list($class, $key) = explode('/', $key);

                $class = Object::getClass($baseClass, $class);

                return $class::getInstance($key, $ttl);
            }
        }

        if (is_string($key) && String::startsWith($key, 'default')) {
            $key = $class::getDefaultKey();
        }

        $data = $key;

        $key = is_string($key)
            ? $class . '/' . $key
            : $class . '/' . md5(Json::encode($key));

        $object = null;
        try {
            $dataProvider = $baseClass::getDataProvider('instance');

            if ($ttl != -1 && $object = $dataProvider->get($key)) {
//                Container::getLogger()->log('(cache) ' . __CLASS__ . ' - ' . $dataProvider->getFullKey($key) . ': ' . get_class($object), Logger::INFO);
                return $object;
            }

            $object = $class::create($data);

            if ($object) {
                $dataProvider->set($key, $object, $ttl);
            }

//            Container::getLogger()->log('(new) ' . __CLASS__ . ' - ' . $dataProvider->getFullKey($key) . ': ' . get_class($object));
        } catch (File_Not_Found $e) {
            if ($baseClass == Code_Generator::getClass()) {
                Container::getLogger()->exception(['Code generator for {$0} not found', $key], __FILE__, __LINE__, $e);
            }

            if (Environment::isDevelopment()) {
                Code_Generator::getLogger()->warning(['File {$0} not found. Trying generate {$1}...', [$key, $baseClass]], __FILE__, __LINE__, $e);
                $baseClass::getCodeGenerator()->generate($key);
                $object = $class::create($key, '');
            } else {
                Container::getLogger()->error(['File {$0} not found', $key], __FILE__, __LINE__, $e);
            }
        }

        if (!$object) {
            self::getLogger()->exception('Could not create object', __FILE__, __LINE__);
        }

        return $object;
    }

    /**
     * Return default class key
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
        Resource::getLogger()->exception(['Implementation {$0} is required for {$1}', [__FUNCTION__, self::getClass()]], __FILE__, __LINE__);
        return null;
    }

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultKey()
    {
        Resource::getLogger()->exception(['Implementation {$0} is required for {$1}', [__FUNCTION__, self::getClass()]], __FILE__, __LINE__);
        return null;
    }

    /**
     * Create instance
     *
     * @param $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function create($key)
    {
        Resource::getLogger()->exception(['Implementation {$0} is required for {$1}', [__FUNCTION__, self::getClass()]], __FILE__, __LINE__, null, $key);
    }

    /**
     * Return logger for self class
     *
     * @return Logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getLogger()
    {
        return Logger::getInstance(self::getClass());
    }
}