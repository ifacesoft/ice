<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 24.12.13
 * Time: 22:55
 */

namespace ice\core;

use ice\core\helper\Dir;
use ice\Exception;
use ice\Ice;

class Config
{
    private $_config = null;
    private $_configName = null;

    public function __construct(array $_config, $configName)
    {
        $this->_config = $_config;
        $this->_configName = $configName;
    }

    /**
     * @param $className
     * @param array $configData
     * @param null $postfix
     * @return Config
     */
    public static function create($className, array $configData, $postfix = null)
    {
        $configName = $postfix
            ? $className . '_' . $postfix
            : $className;

        $fileName = Loader::getFilePath($configName, 'Config', '.php', false, true, true);

        Dir::get(dirname($fileName));

        file_put_contents($fileName, '<?php' . "\n" . 'return ' . var_export($configData, true) . ';');

        return Config::get($className, array(), $postfix, true, false);
    }

    /**
     * @param $className
     * @param array $selfConfig
     * @param null $postfix
     * @param bool $isRequired
     * @param bool $isUseCache
     * @throws Exception
     * @return Config
     */
    public static function get(
        $className,
        array $selfConfig = array(),
        $postfix = null,
        $isRequired = false,
        $isUseCache = true
    )
    {
        if ($postfix) {
            $className .= '_' . $postfix;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam('configDataProviderKey'));

        $_config = $isUseCache ? $dataProvider->get($className) : null;

        if ($_config) {
            return $_config;
        }

        $config = array();

        $fileName = Loader::getFilePath($className, 'Config', '.php', $isRequired);

//        var_dump($fileName . ' ' . file_exists($fileName) . "</br>\n");

        if ($fileName) {
            $configFromFile = include $fileName;

            if (!is_array($configFromFile)) {
                throw new Exception('Не валидный файл конфиг: ' . $fileName);
            }

            $config += $configFromFile; // http://www.php.net/array_merge => оператор +
        }

        $config += $selfConfig;

        if (empty($config)) {
            return null;
        }

        $_config = new Config($config, $className);

        $dataProvider->set($className, $_config);

        return $_config;
    }

    /**
     * @param $key
     * @param bool $isRequired
     * @throws Exception
     * @return string
     */
    public function getParam($key, $isRequired = true)
    {
        $param = null;

        try {
            $param = $this->xpath($this->_config, $key, $isRequired);
        } catch (\Exception $e) {
            throw new Exception('Could nof found config param -> ' . $this->getConfigName() . ':' . $key, array(), $e);
        }

        if (is_array($param)) {
            $param = reset($param);
        }

        return $param;
    }

    /**
     * @param $key
     * @param bool $isRequired
     * @throws Exception
     * @return array
     */
    public function getParams($key = null, $isRequired = true)
    {
        $params = null;

        try {
            $params = $this->xpath($this->_config, $key, $isRequired);
        } catch (\Exception $e) {
            throw new Exception('Could nof found config params -> ' . $this->getConfigName() . ':' . $key, array(), $e);
        }

        if (!is_array($params)) {
            throw new Exception('Ожидается массив данных.', $params);
        }

        return $params;
    }

    private function xpath($config, $key, $isRequired)
    {
        if (!$key) {
            return $config;
        }

        $pos = strpos($key, '/');

        if ($pos === false) {
            $param = isset($config[$key]) ? $config[$key] : null;

            if ($param === null && $isRequired) {
                throw new Exception('Could nof found config required param -> ' . $this->getConfigName() . ':' . $key);
            }

            return (array)$param;
        }

        $_key = substr($key, 0, $pos);
        $param = isset($config[$_key]) ? $config[$_key] : null;

        if ($param === null && $isRequired) {
            throw new Exception('Could nof found config required param -> ' . $this->getConfigName() . ':' . $key);
        }

        return $this->xpath($param, substr($key, $pos + 1), $isRequired);
    }

    /**
     * @return string
     */
    public function getConfigName()
    {
        return $this->_configName;
    }
}
