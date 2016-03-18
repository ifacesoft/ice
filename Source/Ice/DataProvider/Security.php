<?php

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Security as Core_Security;
use Ice\Exception\Core_MethodNotImplemented;
use Ice\Exception\Security_MethodNotSafe;
use Ice\Helper\Php;

class Security extends DataProvider
{
    const DEFAULT_KEY = 'default';

    /**
     * Return default data provider key
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
     * Get instance connection of data provider
     *
     * @throws Exception
     * @return Core_Security
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected function connect(&$connection)
    {
        return $connection = Core_Security::getInstance();
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected function close(&$connection)
    {
        /** @var Core_Security $connection */
        $connection->logout();
        $connection = null;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function get($key = null)
    {
        $method = Php::camelCaseMethodName($key, __FUNCTION__);
        return $this->getConnection()->$method();
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @return mixed setted value
     *
     * @throws Security_MethodNotSafe
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function set($key, $value = null, $ttl = null)
    {
        throw new Security_MethodNotSafe(['Method {$0} not granted in {$1}', [Php::camelCaseMethodName($key, __FUNCTION__), __CLASS__]]);
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function delete($key, $force = true)
    {
        throw new Security_MethodNotSafe(['Method {$0} not granted in {$1}', [Php::camelCaseMethodName($key), __CLASS__]]);
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param  int $step
     * @return mixed new value
     *
     * @throws Security_MethodNotSafe
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function incr($key, $step = 1)
    {
        throw new Security_MethodNotSafe(['Method {$0} not granted in {$1}', [Php::camelCaseMethodName($key), __CLASS__]]);
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param  int $step
     * @return mixed new value
     *
     * @throws Security_MethodNotSafe
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function decr($key, $step = 1)
    {
        throw new Security_MethodNotSafe(['Method {$0} not granted in {$1}', [Php::camelCaseMethodName($key), __CLASS__]]);
    }

    /**
     * Flush all stored data
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function flushAll()
    {
        throw new Security_MethodNotSafe(['Method {$0} not granted in {$1}', [Php::camelCaseMethodName(__FUNCTION__), __CLASS__]]);
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     * @throws Core_MethodNotImplemented
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getKeys($pattern = null)
    {
        throw new Core_MethodNotImplemented(['Method {$0} not implemented in {$1}', [__FUNCTION__, __CLASS__]]);
    }


}