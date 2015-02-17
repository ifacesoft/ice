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
use Ice\Data\Provider\Repository;
use Ice\Exception\File_Not_Found;
use Ice\Helper\Config as Helper_Config;
use Ice\Helper\File;
use Ice\Helper\Object;

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
 */
class Config
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
     * Constructor of config object
     *
     * @param array $config
     * @param $configName
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct($configName, array $config)
    {
        $this->_configName = $configName;
        $this->_config = $config;
    }

    /**
     * Return new Config
     *
     * @param $configName
     * @param array $config
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function create($configName, array $config)
    {
        return new Config($configName, $config);
    }

    /**
     * Get config object by type or key
     *
     * @param Core $class
     * @param null $postfix
     * @param bool $isRequired
     * @param integer $ttl
     * @return Config
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getInstance($class, $postfix = null, $isRequired = false, $ttl = null)
    {
        $baseClass = Object::getBaseClass($class);

        if ($postfix) {
            $class .= '_' . $postfix;
        }

        /** @var Repository $repository */
        $repository = self::getRepository();

        if ($_config = $ttl >= 0 ? $repository->get($class) : null) {
            return $_config;
        }

        $config = [];

//        if (Object::isClass($class) && !empty($class::$configDefaults)) {
//            $config = array_merge_recursive($class::$configDefaults, $config);
//        }
//
//        if (Object::isClass($class) && !empty($class::$config)) {
//            $config = array_merge_recursive($class::$config, $config);
//        }
//
        if ($class != __CLASS__ && $coreConfig = self::getConfig()->gets($class, false)) {
            $config = array_merge_recursive($coreConfig, $config);
        }

        if ($baseClass != $class) {
            foreach (array_reverse(Loader::getFilePath($baseClass, '.php', 'Config/', false, false, false, true)) as $configFilePath) {
                $configFromFile = File::loadData($configFilePath);
                if (!is_array($configFromFile)) {
                    Config::getLogger()->exception(['Не валидный файл конфиг: {$0}', $configFilePath], __FILE__, __LINE__);
                }
                if (isset($configFromFile[$class])) {
                    $config = array_merge_recursive($configFromFile[$class], $config);
                }
            }
        }

        try {
            foreach (array_reverse(Loader::getFilePath($class, '.php', 'Config/', $isRequired, false, false, true)) as $configFilePath) {
                $configFromFile = File::loadData($configFilePath);
                if (!is_array($configFromFile)) {
                    Config::getLogger()->exception(['Не валидный файл конфиг: {$0}', $configFilePath], __FILE__, __LINE__);
                }
                $config = array_merge_recursive($configFromFile, $config);
            }
        } catch (File_Not_Found $e) {
            Config::getLogger()->exception(['Config for {$0} not found', $class], __FILE__, __LINE__, $e, null, -1, 'Ice:Config_Not_Found');
        }

        return $repository->set($class, Config::create($class, $config), $ttl);
    }

//    /**
//     * Retuurn default data provider key
//     *
//     * @return string
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    protected static function getDefaultDataProviderKey()
//    {
//        return 'Ice:Registry/' . __CLASS__;
//    }

    /**
     * Get config param values
     *
     * @param string|null $key
     * @param bool $isRequired
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function gets($key = null, $isRequired = true)
    {
        return Helper_Config::gets($this->_config, $key, $isRequired);
    }

    /**
     * Return default config for class
     *
     * @param $key
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getDefault($key)
    {
        return Config::getConfig()->gets('defaults/' . $key, false);
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
     * @param string|null $key
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
     * Set config param
     *
     * @param $key
     * @param $value
     * @param bool $force
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function set($key, $value, $force = false)
    {
        Helper_Config::set($this->_config, $key, $value, $force);
    }

    /**
     * Remove param
     *
     * @param $key
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function remove($key)
    {
        Helper_Config::remove($this->_config, $key);
    }

    /**
     * Backup config
     *
     * @param $revision
     * @return Config
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function backup($revision)
    {
        File::move(
            Loader::getFilePath($this->getConfigName(), '.php', 'Config/', false, true),
            Loader::getFilePath($this->getConfigName() . '/' . $revision, '.php', 'Var/Backup/Config/', false, true)
        );

        return $this;
    }

    /**
     * Save config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function save()
    {
        File::createData(Loader::getFilePath($this->getConfigName(), '.php', 'Config/', false, true), $this->_config);
        return $this;
    }

    /**
     * Restore object
     *
     * @param array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function __set_state(array $data)
    {
        return new self($data['_configName'], $data['_config']);
    }
}