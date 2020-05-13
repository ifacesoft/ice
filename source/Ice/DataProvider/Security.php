<?php

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Security as Core_Security;
use Ice\Exception\Core_MethodNotImplemented;
use Ice\Exception\Security_MethodNotSafe;
use Ice\Helper\Php;

/**
 * Class Security
 *
 * Data provider for security
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
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
     * Get data from data provider by key
     *
     * @param  string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @author anonymous <email>
     *
     * $todo: Переписать с учетом массивов и т.д.
     *
     * @version 0
     * @since   0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $values = [];

        if (is_array($default)) {
            $key = (array)$key;
        }

        if (is_array($key)) {
            if ($default === null) {
                $default = [];
            }

            foreach ($key as $value) {
                $method = Php::camelCaseMethodName($value, __FUNCTION__);
                $values[$value] = $this->getConnection()->$method();
            }

            return $values ? $values : $default;
        }

        $method = Php::camelCaseMethodName($key, __FUNCTION__);
        $value = $this->getConnection()->$method();

        return $value ? $value : $default;
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
     * Set data to data provider
     *
     * @param array $values
     * @param null $ttl
     * @return array
     *
     * @throws Security_MethodNotSafe
     * @throws \Ice\Exception\FileNotFound
     *
     * @author anonymous <email>
     *
     * @version 1.2
     * @since   1.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        throw new Security_MethodNotSafe(['Method not granted in {$0}', __CLASS__]);
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
     * @throws Exception
     */
    protected function close(&$connection)
    {
        /** @var Core_Security $connection */
        $connection->logout();
        $connection = null;
    }


    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function expire($key, $ttl)
    {
        // TODO: Implement expire() method.
    }

    /**
     * Check for errors
     *
     * @return void
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    function checkErrors()
    {
        // TODO: Implement checkErrors() method.
    }
}