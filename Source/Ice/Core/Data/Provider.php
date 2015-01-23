<?php
/**
 * Ice core data provider abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Helper\Object;

/**
 * Class Data_Provider
 *
 * Core data provider abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Data_Provider
{
    use Ice\Core;

    const PREFIX_KEY_DELIMETER = '/_';

    /**
     * Stored data providers
     *
     * @var Data_Provider[]
     */
    private static $_dataProviders = [];

    /**
     * Connection of data provider
     *
     * @var mixed
     */
    private $_connection = null;

    /**
     * Data provider key
     *
     * @var string
     */
    private $_key = null;

    /**
     * Data provider index
     *
     * @var string
     */
    private $_index = null;

    /**
     * Data provider scheme
     *
     * @var string
     */
    private $_scheme = null;

    /**
     * Constructor of Data provider
     *
     * @param $key
     * @param $index
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct($key, $index)
    {
        $this->_key = $key;
        $this->_index = $index;
    }

    /**
     * Return new instance of data provider
     *
     * @param $dataProviderKey
     * @param string $index
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getInstance($dataProviderKey = null, $index = 'default')
    {
        /** @var Data_Provider $class */
        $class = self::getClass();

        if (empty($dataProviderKey)) {
            $dataProviderKey = $class::getDefaultDataProviderKey();
        } else if ($class != __CLASS__) {
            $dataProviderKey = $class . '/' . $dataProviderKey;
        }

        list($class, $key) = explode('/', $dataProviderKey);

        $class = Object::getClass(__CLASS__, $class);

        if ($key == 'default') {
            $key = $class::getDefaultKey();
        }

        /** @var string $class */
        if (isset(self::$_dataProviders[$class][$key][$index])) {
            return self::$_dataProviders[$class][$key][$index];
        }

        return self::$_dataProviders[$class][$key][$index] = new $class($key, $index);
    }

    /**
     * Return default data provider key
     *
     * @return null
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultDataProviderKey()
    {
        Data_Provider::getLogger()->fatal('Need implements', __FILE__, __LINE__);
        return null;
    }

    /**
     * Return current scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * Set new data provider scheme
     *
     * @param string $scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setScheme($scheme)
    {
        $this->_scheme = $scheme;
    }

    /**
     * Get instance connection of data provider
     *
     * @throws Exception
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConnection()
    {
        if ($this->_connection !== null) {
            return $this->_connection;
        }

        if (!$this->connect($this->_connection)) {
            /** @var Data_Provider $class */
            $class = __CLASS__;
            $class::getLogger()->fatal(['Data provider "{$0}" connection failed', get_class($this) . '/' . $this->getKey() . ' (index: ' . $this->getIndex() . ')'], __FILE__, __LINE__);
        }

        return $this->_connection;
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract protected function connect(&$connection);

    /**
     * Get current data provider key
     *
     * @return string
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Get current data provider index
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * Close self connection
     *
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function closeConnection()
    {
        if (!$this->close($this->_connection)) {
            /** @var Data_Provider $class */
            $class = __CLASS__;
            $class::getLogger()->fatal(['Не удалось закрыть соединенеие с дата провайдером {$0}', get_class($this) . '/' . $this->getKey() . ' (index: ' . $this->getIndex() . ')'], __FILE__, __LINE__);
        }

        $this->_connection = null;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract protected function close(&$connection);

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function get($key = null);

    /**
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @return mixed setted value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function set($key, $value, $ttl = null);

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function delete($key, $force = true);

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function inc($key, $step = 1);

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function dec($key, $step = 1);

    /**
     * Flush all stored data
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function flushAll();

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function getKeys($pattern = null);

    /**
     * Return full key
     *
     * @param $key
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function getFullKey($key)
    {
        return $this->getKeyPrefix() . Data_Provider::PREFIX_KEY_DELIMETER . $key;
    }

    /**
     * Return prefix of key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function getKeyPrefix()
    {
        /** @var Data_Provider $class */
        $class = get_class($this);
        return str_replace('\\', '/', Data_Provider::getModuleAlias() . '/' . $class::getClassName() . '/' . $this->getKey() . '/' . $this->getIndex());
    }

    /**
     * Return connection options
     *
     * @param $class
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function getOptions($class)
    {
        $options = [
            'ttl' => 3600
        ];

        foreach (Environment::getInstance()->gets(__CLASS__ . '/' . $class . '/' . $this->getIndex()) as $key => $value) {
            $options[$key] = is_array($value) ? reset($value) : $value;
        }

        return $options;
    }
}