<?php
/**
 * Ice core container abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\FileNotFound;
use Ice\Helper\Object;

/**
 * Class Container
 *
 * Core container abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Container
{
    private $instanceKey = null;

    /**
     * Get instance from container
     *
     * @param  string $key
     * @param  null $ttl
     * @param array $params
     * @return null|object|string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public static function getInstance($key, $ttl = null, array $params = [])
    {
        /** @var Container|Core $class */
        $class = get_called_class();

        /** @var Container|Core $baseClass */
        $baseClass = $class::getBaseClass();

        if (is_object($key) && $key instanceof $baseClass) {
            return $key;
        }

        if ($class == $baseClass) {
            if (!$key) {
                return $baseClass::getInstance($class::getDefaultClassKey(), $ttl, $params);
            } elseif (is_string($key)) {
                $parts = explode('/', $key);

                if (count($parts) == 1) {
                    $class = $key;
                    $key = 'default';
                } else {
                    $class = $parts[0];
                    $key = $parts[1];
                }

                $class = Object::getClass($baseClass, $class);

                return $class::getInstance($key, $ttl, $params);
            }
        }

        if (!$key || $key == 'default') {
            $key = $class::getDefaultKey();
        }

        $logger = Logger::getInstance(__CLASS__);

        $object = null;
        try {
            $dataProvider = $class::getDataProvider('instance');

            if ($ttl != -1 && $object = $dataProvider->get($key)) {
                return $object;
            }

            $params['instanceKey'] = $key;

            $object = $class::create($params);

            if ($object) {
                $dataProvider->set($key, $object, $ttl);
            }

        } catch (FileNotFound $e) {
            if ($baseClass == Code_Generator::getClass()) {
                $logger->exception(['Code generator for {$0} not found', $key], __FILE__, __LINE__, $e);
            }

            if (Environment::getInstance()->isDevelopment()) {
                $baseClass::getCodeGenerator()->generate($key);
                $object = $class::create($key);
            } else {
                $logger->error(['File {$0} not found', $key], __FILE__, __LINE__, $e);
            }
        }

        if (!$object) {
            $class::getLogger()->exception('Could not create object', __FILE__, __LINE__);
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
     * @since   0.4
     */
    protected static function getDefaultClassKey()
    {
        Resource::getLogger()->exception(
            ['Implementation {$0} is required for {$1}', [__FUNCTION__, get_called_class()]],
            __FILE__,
            __LINE__
        );

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
     * @since   0.4
     */
    protected static function getDefaultKey()
    {
        Resource::getLogger()->exception(
            ['Implementation {$0} is required for {$1}', [__FUNCTION__, get_called_class()]],
            __FILE__,
            __LINE__
        );

        return null;
    }

    /**
     * Create instance
     *
     * @param  $params
     * @return Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.4
     */
    final private static function create($params)
    {
        $class = get_called_class();

        /** @var Container $object */
        $object = new $class();

        $object->instanceKey = $params['instanceKey'];

        $object->init($params);

        return $object;
    }

    /**
     * @return string
     */
    public function getInstanceKey()
    {
        return $this->instanceKey;
    }

    /**
     * Init object
     *
     * @param array $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected abstract function init(array $params);
}
