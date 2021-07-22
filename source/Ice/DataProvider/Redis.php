<?php
/**
 * Ice data provider implementation redis class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Exception\Error;

/**
 * Class Redis
 *
 * Data provider for redis storage
 *
 * @see \Ice\Core\DataProvider
 *
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Redis extends DataProvider
{
    const DEFAULT_KEY = 'default';

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
        return self::DEFAULT_KEY;
    }

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param null $ttl
     * @return array
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        if ($ttl === null) {
            $ttl = $this->getOptions()->get('ttl', 3600);
        }

        $connection = $this->getConnection();

        foreach ($values as $key => $value) {
            $fullKey = $this->getFullKey($key);

            if (is_numeric($value)) {
                $connection->rawCommand('set', $fullKey, $value);
                $connection->setTimeout($fullKey, $ttl);

                $this->checkErrors();

                continue;
            }

            $connection->set($fullKey, $value, $ttl);
            $this->checkErrors();
        }

        return $values;
    }

    /**
     * Return data provider redis connection
     *
     * @return \Redis
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Check for errors
     * @return void
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function checkErrors()
    {
        if ($error = $this->getConnection()->getLastError()) {
            $this->getConnection()->clearLastError();
            throw new Error($error);
        }
    }

    /**
     * Set data to data provider
     *
     * @param $key
     * @param array $values
     * @param null $ttl
     * @return array
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.6
     */
    public function hSet($key, array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            dump([$key, $values, $ttl]);
            return $values;
        }

        if ($ttl === null) {
            $ttl = $this->getOptions()->get('ttl', 3600);
        }

        $fullKey = $this->getFullKey($key);

        foreach ($values as $field => $value) {
            $this->getConnection()->hset($fullKey, $field, $value);
            $this->checkErrors();
        }

        if ($ttl === true) {
            $this->persist($fullKey);
        } else {
            $this->expire($fullKey, $ttl);
        }

        return $values;
    }

    /**
     * @param $key
     * @return bool
     * @throws Exception
     * @todo Define in parent (need for all providers)
     */
    public function persist($key)
    {
        $connection = $this->getConnection();

        $return = true;

        foreach ((array)$key as $k) {
            if (!$connection->persist($k)) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param int $ttl
     * @return mixed new value
     *
     * @throws Exception
     * @version 1.6
     * @since   1.6
     * @author anonymous <email>
     *
     */
    public function expire($key, $ttl)
    {
        $connection = $this->getConnection();

        $return = true;

        foreach ((array)$key as $k) {
            if (!$connection->expire($k, $ttl)) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @return mixed|boolean
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        if ($force) {
            $this->getConnection()->del($this->getFullKey($key));
            return true;
        }

        $value = $this->get($key);

        $this->getConnection()->del($this->getFullKey($key));

        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $value = $this->getConnection()->get($this->getFullKey($key));

        if ($value === false) {
            $value = $default;
        }

        if ($value === null && $require) {
            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
        }

        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @param null $field
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.6
     */
    public function hGet($key, $field = null, $default = null, $require = false)
    {
        $value = $field
            ? $this->getConnection()->hGet($this->getFullKey($key), $field)
            : $this->getConnection()->hGetAll($this->getFullKey($key));

        if ($value === false) {
            $value = $default;
        }

        if ($value === null && $require) {
            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
        }

        return $value;
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param int $step
     * @return mixed new value
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.9
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        $value = $this->getConnection()->incrBy($this->getFullKey($key), $step);
        $this->checkErrors();

        return $value;
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param int $step
     * @return mixed new value
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.9
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        $value = $this->getConnection()->decrBy($this->getFullKey($key), $step);
        $this->checkErrors();

        return $value;
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function flushAll()
    {
        $this->getConnection()->del($this->getConnection()->keys($this->getKeyPrefix() . '*'));
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getKeys($pattern = null)
    {
        $keyPrefix = $this->getKeyPrefix() . DataProvider::PREFIX_KEY_DELIMETER;

        $size = strlen($keyPrefix);

        $keys = [];

        foreach ($this->getConnection()->keys(addslashes($keyPrefix . $pattern . '*')) as $key) {
            $keys[] = substr($key, $size);
        }

        return $keys;
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return bool
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        $connection = new \Redis();

        try {
            $options = $this->getOptions();
            
            list($authHost, $port, $timeout) = $options->getParams(['host', 'port', 'timeout']);

            $auth = null;

            if (strpos($authHost, '@')) {
                list($auth, $host) = explode('@', $authHost);
            } else {
                $host = $authHost;
            }

            if (!$connection->connect($host, $port, $timeout, null, 500)) {
                Logger::getInstance(__CLASS__)
                    ->error(
                        [
                            'Redis not connected ({$0} {$1}:{$2}): {$3}',
                            [$options->getName(), $host, $port, $connection->getLastError()]
                        ],
                        __FILE__,
                        __LINE__
                    );

                return null;
            }

            if ($auth) {
                if (!$connection->auth($auth)) {
                    Logger::getInstance(__CLASS__)
                        ->error(
                            [
                                'Redis not authenticated ({$0} {$1}:{$2}): {$3}',
                                [$options->getName(), $host, $port, $connection->getLastError()]
                            ],
                            __FILE__,
                            __LINE__
                        );
                    
                    return null;
                }
            }

            if (function_exists('igbinary_serialize') && defined('\Redis::SERIALIZER_IGBINARY')) {
                $connection->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
            } else {
                $connection->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            }

            // $connection->setOption(\Redis::OPT_PREFIX, 'ice:');

            if ($connection->ping()) {
                return $connection;
            }

                 Logger::getInstance(__CLASS__)
                     ->error(
                         [
                             'Redis not pinged ({$0} {$1}:{$2}): {$3}',
                             [$options->getName(), $host, $port, $connection->getLastError()]
                         ],
                         __FILE__,
                         __LINE__
                     );

            return null;
        } catch (\Throwable $e) {
            Logger::getInstance(__CLASS__)
                ->error(
                    [
                        'Redis failed ({$0} {$1}:{$2}): {$3}',
                        [$options->getName(), $host, $port, $connection->getLastError()]
                    ],
                    __FILE__,
                    __LINE__
                );

            return null;
         } catch (\Exception $e) {
            Logger::getInstance(__CLASS__)
                ->error(
                    [
                        'Redis failed ({$0} {$1}:{$2}): {$3}',
                        [$options->getName(), $host, $port, $connection->getLastError()]
                    ],
                    __FILE__,
                    __LINE__
                );

            return null;
        }
    }

    /**
     * Close connection with data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    protected function close(&$connection)
    {
        $this->getConnection()->close();
        return true;
    }
}
