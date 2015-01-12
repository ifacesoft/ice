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
use Ice\Helper\Object;

/**
 * Class Container
 *
 * Core container abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Container
{
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
     * @version 0.0
     * @since 0.0
     */
    public static function getInstance($key = null, $ttl = null)
    {
        /** @var Container $class */
        $class = self::getClass();

        if (empty($key)) {
            $key = $class::getDefaultKey();
        }

        /** @var Core $baseClass */
        $baseClass = $class::getBaseClass();

        $data = null;
        if (is_string($key)) {
            if ($class == $baseClass) {
                $key = $baseClass::getClass($key);
            }
            $data = $key;
        } else {
            $data = $key;
            $key = md5(serialize($key));
        }

        $object = null;
        try {
            if (in_array('Ice\Core\Cacheable', class_implements($baseClass))) {
                /** @var Cacheable $class */
                $object = $class::getCache($data, $key);
            } else {
                $dataProvider = $class::getDataProvider('instance');

                if ($ttl != -1 && $object = $dataProvider->get($key)) {
                    return $object;
                }

                $object = $class::create($data, $key);

                if ($object) {
                    $dataProvider->set($key, $object, $ttl);
                }
            }
        } catch (File_Not_Found $e) {
            if ($baseClass == Code_Generator::getClass()) {
                Container::getLogger()->fatal(['Code generator for {$0} not found', $key], __FILE__, __LINE__, $e);
            }

            if (Environment::isDevelopment()) {
                Code_Generator::getLogger()->warning(['File {$0} not found. Trying generate {$1}...', [$key, $baseClass]], __FILE__, __LINE__, $e);
                $baseClass::getCodeGenerator()->generate($key);
                $object = $class::create($key);
            } else {
                Container::getLogger()->error(['File {$0} not found', $key], __FILE__, __LINE__, $e);
            }
        }

        if (!$object) {
            self::getLogger()->fatal('Could not create object', __FILE__, __LINE__);
        }

        return $object;
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