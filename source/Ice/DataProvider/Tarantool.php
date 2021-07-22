<?php
/**
 * Ice data provider implementation tarantool class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Exception\Error;
use Tarantool\Client\Client;
use Tarantool\Client\Connection\StreamConnection;
use Tarantool\Client\Packer\PurePacker;
use Tarantool\Client\Schema\Space;

/**
 * Class Tarantool
 *
 * Data provider for tarantool storage
 *
 * @see \Ice\Core\DataProvider
 *
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Tarantool extends DataProvider
{
    const DEFAULT_KEY = 'default';

    const ITERATOR_EQ = 0;
    const ITERATOR_REQ = 1;
    const ITERATOR_ALL = 2;
    const ITERATOR_LT = 3;
    const ITERATOR_LE = 4;
    const ITERATOR_GE = 5;
    const ITERATOR_GT = 6;
    const ITERATOR_BITS_ALL_SET = 7;
    const ITERATOR_BITSET_ALL_SET = 7;
    const ITERATOR_BITS_ANY_SET = 8;
    const ITERATOR_BITSET_ANY_SET = 8;
    const ITERATOR_BITS_ALL_NOT_SET = 9;
    const ITERATOR_BITSET_ALL_NOT_SET = 9;
    const ITERATOR_OVERLAPS = 10;
    const ITERATOR_NEIGHBOR = 11;

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
     * @param  null $ttl
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
        $values[0] = $this->getSequence();

        $this->getConnection()->insert($values);

        return $values;
    }

    /**
     * Return data provider tarantool connection
     *
     * @return Space
     *
     * @throws Exception
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
     * Check for errors
     * @return void
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function checkErrors()
    {
//        throw new Error('Not implemented');
//        if ($error = $this->getConnection()->getLastError()) {
//            $this->getConnection()->clearLastError();
//            throw new Error($error);
//        }
    }

    /**
     * Set data to data provider
     *
     * @param $key
     * @param array $values
     * @param  null $ttl
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
        throw new Error('Not implemented');
//        if ($ttl === -1) {
//            return $values;
//        }
//
//        if ($ttl === null) {
//            $ttl = $this->getOptions()->get('ttl', 3600);
//        }
//
//        $fullKey = $this->getFullKey($key);
//
//        foreach ($values as $field => $value) {
//            $this->getConnection()->hset($fullKey, $field, $value);
//            $this->checkErrors();
//        }
//
//        if ($ttl) {
//            $this->expire($fullKey, $ttl);
//        }
//
//        return $values;
    }

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
     * @return mixed new value
     *
     * @throws Error
     * @author anonymous <email>
     *
     * @version 1.6
     * @since   1.6
     */
    public function expire($key, $ttl)
    {
        throw new Error('Not implemented');
//        $connection = $this->getConnection();
//
//        $return = true;
//
//        foreach ((array)$key as $k) {
//            if (!$connection->expire($k, $ttl)) {
//                $return = false;
//            }
//        }
//
//        return $return;
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
     * @version 0.0
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        throw new Error('Not implemented');
//        if ($force) {
//            $this->getConnection()->delete($this->getFullKey($key));
//            return true;
//        }
//
//        $value = $this->get($key);
//
//        $this->getConnection()->delete($this->getFullKey($key));
//
//        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
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
        $value = $this->getConnection()->select([$key])->getData()[0];

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
     * @param  string $key
     * @param null $field
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.6
     */
    public function hGet($key, $field = null, $default = null, $require = false)
    {
        throw new Error('Not implemented');
//        $value = $field
//            ? $this->getConnection()->hget($this->getFullKey($key), $field)
//            : $this->getConnection()->hGetAll($this->getFullKey($key));
//
//        if ($value === false) {
//            $value = $default;
//        }
//
//        if ($value === null && $require) {
//            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
//        }
//
//        return $value;
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
     * @version 1.9
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        throw new Error('Not implemented');
//        $value = $this->getConnection()->incrBy($this->getFullKey($key), $step);
//        $this->checkErrors();
//
//        return $value;
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
     * @version 1.9
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        throw new Error('Not implemented');
//        $value = $this->getConnection()->decrBy($this->getFullKey($key), $step);
//        $this->checkErrors();
//
//        return $value;
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
        throw new Error('Not implemented');
//        $this->getConnection()->delete($this->getConnection()->getKeys($this->getKeyPrefix() . '*'));
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return void
     *
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getKeys($pattern = null)
    {
        throw new Error('Not implemented');
//        $keyPrefix = $this->getKeyPrefix() . DataProvider::PREFIX_KEY_DELIMETER;
//
//        $size = strlen($keyPrefix);
//
//        $keys = [];
//
//        foreach ($this->getConnection()->getKeys(addslashes($keyPrefix . $pattern . '*')) as $key) {
//            $keys[] = substr($key, $size);
//        }
//
//        return $keys;
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
        $options = $this->getOptions();
        
        list($host, $port) = $options->getParams(['host', 'port']);
        
        $client = Client::fromDsn('tcp://' . $host . ':' . $port);

        $connection = $client->getSpace($this->getIndex());

        $isConnected = (boolean)$connection;

        if (!$isConnected) {
            Logger::getInstance(__CLASS__)
                ->error(
                    [
                        'Tarantool failed ({$0} {$1}:{$2})',
                        [$options->getName(), $host, $port]
                    ],
                    __FILE__,
                    __LINE__
                );
            
            return null;
        }

        return $isConnected;
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
        $this->getConnection()->close();
        return true;
    }
}
