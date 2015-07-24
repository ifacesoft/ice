<?php
/**
 * Ice data provider implementation registry class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use ArrayObject;
use Ice\Core\Data_Provider;
use Ice\Core\Debuger;
use Ice\Core\Exception;

/**
 * Class Registry
 *
 * Data provider for registry data
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Data_Provider
 */
class Registry extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Registry/default';
    const DEFAULT_KEY = 'instance';

    private static $count = [];

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultDataProviderKey()
    {
        return self::DEFAULT_DATA_PROVIDER_KEY;
    }

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        $keyPrefix = $this->getKeyPrefix();
        $data = $this->getConnection()->$keyPrefix;

        if ($force) {
            unset($data[$key]);
            $this->getConnection()->$keyPrefix = $data;
            return true;
        }

        $value = $data[$key];

        unset($data[$key]);
        $this->getConnection()->$keyPrefix = $data;

        return $value;
    }

    /**
     * Return registry connection
     *
     * @return ArrayObject
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    0.4 need refactoring // long time execute
     * @version 0.4
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        return $this->set($key, $this->get($key) + $step);
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  integer $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function set($key, $value = null, $ttl = 0)
    {
        if (is_array($key) && $value === null) {
            foreach ($key as $index => $value) {
                $this->set($index, $value, $ttl);
            }

            return $key;
        }

        if ($ttl == -1) {
            return $value;
        }

        $keyPrefix = $this->getKeyPrefix();

//        if (!isset(Registry::$count[$keyPrefix])) {
//            Registry::$count[$keyPrefix] = 0;
//        }

        if (!isset($this->getConnection()->$keyPrefix)) {
            $this->getConnection()->$keyPrefix = [$key => $value];
//            Registry::$count[$keyPrefix]++;
//            Debuger::dump($keyPrefix . ': ' . Registry::$count[$keyPrefix]);
            return $value;
        }

        $data = $this->getConnection()->$keyPrefix;
        $data[$key] = $value;
        $this->getConnection()->$keyPrefix = $data;
//        Registry::$count[$keyPrefix]++;
//        Debuger::dump($keyPrefix . ': ' . Registry::$count[$keyPrefix]);
        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($key = null)
    {
        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            return null;
        }

        $data = $this->getConnection()->$keyPrefix;

        if (empty($key)) {
            return $data;
        }

        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    0.4 need refactoring // long time execute
     * @version 0.0
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        return $this->set($key, $this->get($key) - $step);
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function flushAll()
    {
        $keyPrefix = $this->getKeyPrefix();
        $this->getConnection()->$keyPrefix = [];
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getKeys($pattern = null)
    {
        return array_keys($this->get());
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        $connection = new ArrayObject();
        return true;
    }

    /**
     * Close connection with data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }
}
