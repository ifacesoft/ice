<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.12.13
 * Time: 22:06
 */

namespace ice\core;

use ice\Exception;
use ice\Ice;

abstract class Data_Provider
{
    const KEY = 'dataProviderKey';
    const DEFAULT_SCHEME = 'default';

    private static $_connectionPool = array();
    /** @var Data_Provider[] */
    private static $_dataProviders = array();

    private $_name = null; // Redis || Mysqli
    private $_index = null; // default || production
    private $_scheme = null;
    private $_options = null;

    private function __construct($name, $index, $scheme, array $options = array())
    {
        $this->_name = $name;
        $this->_index = $index;
        $this->_scheme = $scheme;
        $this->_options = $options;
    }

    public function setScheme($scheme)
    {
        $this->_scheme = $scheme;
    }

    /**
     * @throws Exception
     * @return null
     */
    public function getConnection()
    {
        $dataProviderName = $this->getName();
        $dataProviderIndex = $this->getIndex();
        $dataProviderScheme = $this->getScheme();

        if (isset(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme])) {
            return self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme];
        }

        if (!isset(self::$_connectionPool[$dataProviderName])) {
            self::$_connectionPool[$dataProviderName] = array();
        }

        if (!isset(self::$_connectionPool[$dataProviderName][$dataProviderIndex])) {
            self::$_connectionPool[$dataProviderName][$dataProviderIndex] = array();
        }

        if (!empty(self::$_connectionPool[$dataProviderName][$dataProviderIndex])) {
            $oldConnection = each(self::$_connectionPool[$dataProviderName][$dataProviderIndex]);

            self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]
                = self::$_connectionPool[$dataProviderName][$dataProviderIndex][$oldConnection['key']];


            if (!$this->switchScheme(
                self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]
            )
            ) {
                unset(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]);
                throw new Exception('Не удалось переключиться к схеме дата провайдера "' . $this->getDataProviderKey() . '"');
            }

            unset(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$oldConnection['key']]);

            return self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme];
        }

        self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme] = null;

        if (!$this->connect(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme])) {
            throw new Exception('Соединение с дата провайдером "' . $this->getDataProviderKey() . '" не установлено');
        }

        if (!$this->switchScheme(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme])) {
            unset(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]);
            throw new Exception('Не удалось переключиться к схеме дата провайдера "' . $this->getDataProviderKey() . '"');
        }

        return self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme];
    }

    public function closeConnection()
    {
        $dataProviderName = $this->getName();
        $dataProviderIndex = $this->getIndex();
        $dataProviderScheme = $this->getScheme();

        if (!self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]) {
            return;
        }

        if (!$this->close(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme])) {
            throw new Exception('Не удалось закрыть соединенеие с дата провайдером "' . $this->getDataProviderKey() . '"');
        }

        unset(self::$_connectionPool[$dataProviderName][$dataProviderIndex][$dataProviderScheme]);
    }

    /**
     * @param $dataProviderKey // example: 'Redis:localhost/model'
     * @return Data_Provider
     */
    public static function getInstance($dataProviderKey)
    {
        $index = strstr($dataProviderKey, '/', true);
        $dataProviderScheme = substr(strstr($dataProviderKey, '/'), 1);

        if (empty($dataProviderScheme)) {
            $dataProviderScheme = self::DEFAULT_SCHEME;
        }

        $options = Ice::getConfig()->getParams('dataProviders/' . $index);

        list($dataProviderName, $dataProviderIndex) = explode(':', $index);

        if (isset(self::$_dataProviders[$dataProviderName][$dataProviderIndex][$dataProviderScheme])) {
            return self::$_dataProviders[$dataProviderName][$dataProviderIndex][$dataProviderScheme];
        }

        $dataProviderClass = 'ice\data\provider\\' . $dataProviderName;

        if (empty(self::$_dataProviders[$dataProviderName])) {
            $filePath = '';

            foreach (explode('\\', $dataProviderClass) as $filePathPart) {
                $filePathPart[0] = strtoupper($filePathPart[0]);
                $filePath .= '/' . $filePathPart;
            }

            $modulesConfigName = Ice::getConfig()->getConfigName() . ':modules';

            foreach (Ice::getConfig()->getParams('modules') as $module) {
                $moduleConfig = new Config($module, $modulesConfigName);
                $fileName = dirname($moduleConfig->getParam('path')) . str_replace('_', '/', $filePath) . '.php';
                if (file_exists($fileName)) {
                    require_once $fileName;
                    break;
                }
            }
        }

        self::$_dataProviders[$dataProviderName][$dataProviderIndex][$dataProviderScheme] =
            new $dataProviderClass($dataProviderName, $dataProviderIndex, $dataProviderScheme, $options);

        return self::$_dataProviders[$dataProviderName][$dataProviderIndex][$dataProviderScheme];
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    public function getDataProviderKey()
    {
        return $this->getName() . '_' . $this->getIndex();
    }

    protected function getKeyPrefix()
    {
        return $this->getDataProviderKey() . '_' . $this->getScheme();
    }

    protected function getKey($key)
    {
        return Ice::getProject() . '_' . urlencode($this->getKeyPrefix() . '_' . $key);
    }

    protected function getOption($key = null)
    {
        return $key ? $this->_options[$key] : $this->_options;
    }

    /**
     * @param $connection
     * @return boolean
     */
    abstract protected function switchScheme(&$connection);

    /**
     * @param $connection
     * @return boolean
     */
    abstract protected function connect(&$connection);

    /**
     * @param $connection
     * @return boolean
     */
    abstract protected function close(&$connection);

    abstract public function get($key = null);

    abstract public function set($key, $value, $ttl = 3600);

    abstract public function delete($key);

    abstract public function inc($key, $step = 1);

    abstract public function dec($key, $step = 1);

    abstract public function flushAll();
}