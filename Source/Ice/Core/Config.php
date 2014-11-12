<?php
/**
 * Ice core config class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Object;
use Ice\Helper\File;
use Iterator;

/**
 * Class Config
 *
 * Core config class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Config implements Iterator
{
    use Core;

    /**
     * Config params
     *
     * @var array
     */
    private $_config = null;

    /**
     * Config Key
     *
     * @var string
     */
    private $_configName = null;

    /**
     * Current iteration position
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Constructor of config object
     *
     * @param array $_config
     * @param $configName
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct(array $_config, $configName)
    {
        $this->_config = $_config;
        $this->_configName = $configName;
    }

//    /**
//     * Create file config
//     *
//     * @param $className
//     * @param array $configData
//     * @param null $postfix
//     * @param bool $force save only $configData
//     * @throws Exception
//     * @return Config
//     */
//    public static function create($className, array $configData, $postfix = null, $force = false, $moduleName = null)
//    {
//        if (empty($configData)) {
//            throw new Exception('Create config file for class "' . $className . '" failed. Data is empty.');
//        }
//
//        $configName = $postfix
//            ? $className . '_' . $postfix
//            : $className;
//
//        if (!$moduleName) {
//            $moduleName = MODULE;
//        }
//
//        $fileName = Module::get($moduleName) . 'Config/' . str_replace(['_', '\\'], '/', $configName) . '.php';
//
//        if (!$force) {
//            if (file_exists($fileName)) {
//                $configData = array_merge(File::loadData($fileName), $configData);
//            }
//        }
//
//        File::createData($fileName, $configData);
//
//        return Config::getInstance($className, [], $postfix, true, false);
//    }

    /**
     * Get config object by type or key
     *
     * @param Core $class
     * @param array $selfConfig
     * @param null $postfix // TODO: Когда-нибудь выпилить
     * @param bool $isRequired
     * @param bool $isUseCache
     * @throws Exception
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getInstance(
        $class,
        array $selfConfig = [],
        $postfix = null,
        $isRequired = false,
        $isUseCache = true
    )
    {
        $config = [];

        if (!empty($class::$configDefaults)) {
            $config = array_merge_recursive($class::$configDefaults, $selfConfig);
        }

        if (!empty($class::$config)) {
            $config = array_merge_recursive($class::$config, $selfConfig);
        }

        $baseClass = $class::getBaseClass();

        if ($postfix) {
            $class .= '_' . $postfix;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Object::getInstance(Config::getDefaultDataProviderKey());

        $_config = $isUseCache ? $dataProvider->get($class) : null;

        if ($_config) {
            return $_config;
        }

        if ($baseClass != $class) {
            foreach (Loader::getFilePath($baseClass, '.php', 'Config/', $isRequired, false, false, true) as $configFilePath) {
                $configFromFile = File::loadData($configFilePath);
                if (!is_array($configFromFile)) {
                    throw new Exception('Не валидный файл конфиг: ' . $configFilePath);
                }
                if (isset($configFromFile[$class]))
                    $config = array_merge_recursive($config, $configFromFile[$class]);
            }
        }

        foreach (Loader::getFilePath($class, '.php', 'Config/', $isRequired, false, false, true) as $configFilePath) {
            $configFromFile = File::loadData($configFilePath);
            if (!is_array($configFromFile)) {
                throw new Exception('Не валидный файл конфиг: ' . $configFilePath);
            }
            $config = array_merge_recursive($config, $configFromFile);
        }

        if ($class != __CLASS__) {
            $iceConfig = self::getConfig()->gets($class, false);

            if (!empty($iceConfig)) {
                $config = array_merge_recursive($config, $iceConfig);
            }
        }

        $config = array_merge_recursive($config, $selfConfig);

        $_config = new Config($config, $class);

        $dataProvider->set($class, $_config);

        return $_config;
    }

    /**
     * Retuurn default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultDataProviderKey()
    {
        return 'Ice:Object/' . __CLASS__;
    }

    /**
     * Return default key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return self::getClass();
    }

    /**
     * Get more then one params of config
     *
     * @param $key
     * @param bool $isRequired
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function gets($key = null, $isRequired = true)
    {
        if (empty($key)) {
            return $this->_config;
        }

        $params = $this->isSetKey($key);

        if ($params === false) {
            if ($isRequired) {
                Config::getLogger()->fatal(['Could not found required param {$0} for class {$1}', [$key, $this->getConfigName()]], __FILE__, __LINE__);
            }

            return [];
        }

        return (array)$params;
    }

    /**
     * Check is set key in config
     *
     * @param $key
     * @return array|bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function isSetKey($key)
    {
        $params = $this->_config;

        foreach (explode('/', $key) as $keyPart) {
            if (!isset($params[$keyPart])) {
                return false;
            }

            $params = $params[$keyPart];
        }

        return $params;
    }

    /**
     * Return config name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConfigName()
    {
        return $this->_configName;
    }

    /**
     * Get config param value
     *
     * @param $key
     * @param bool $isRequired
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($key = null, $isRequired = true)
    {
        $params = $this->gets($key, $isRequired);

        return empty($params) ? null : reset($params);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function current()
    {
        return new Config((array)current($this->_config), $this->_configName . '_' . $this->_position);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function next()
    {
        next($this->_config);
        ++$this->_position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function valid()
    {
        $var = current($this->_config); // todo: may be (bool) current($this->_config)
        return !empty($var);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function rewind()
    {
        if (!empty($this->_config)) {
            reset($this->_config);
        }

        $this->_position = 0;
    }
}