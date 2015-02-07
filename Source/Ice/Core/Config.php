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

        $baseClass = Object::getBaseClass($class);

        if ($postfix) {
            $class .= '_' . $postfix;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(Config::getDefaultDataProviderKey());

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
        return 'Ice:Registry/' . __CLASS__;
    }

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
}