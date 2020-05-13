<?php

namespace Ice\Helper;

use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Filter;
use Ice\DataProvider\Cli;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\DataProvider\Session;
use Ice\Exception\Error;
use Ice\Exception\Not_Good;

class Input
{
    /**
     * Gets all declared input data
     *
     * @param array $configInput Declared input params
     * @param array $data Addition input data
     * @param string $class
     * @return array
     */
    public static function get(array $configInput, array $data = [], $class = __CLASS__)
    {
        $input = [];

        foreach ($configInput as $name => $param) {
            if (is_int($name)) {
                $name = $param;
                $param = [];
            }

            if (!is_array($param)) {
                $input[$name] = $param;
                continue;
            }

            $dataProviderKeys = isset($param['providers'])
                ? ($param['providers'] === '*'
                    ? ['default', Request::class, Router::class, Cli::class, Session::class]
                    : (array)$param['providers']
                )
                : ['default'];

            $default = array_key_exists('default', $param) ? $param['default'] : null;

            foreach ($dataProviderKeys as $key => $dataProviderKey) {
                if (array_key_exists($name, $input) && $input[$name] !== null) {
                    break;
                }

                $index = DataProvider::DEFAULT_INDEX;

                if (is_string($key)) {
                    $index = $dataProviderKey;
                    $dataProviderKey = $key;
                }

                if ($dataProviderKey === 'default') {
                    if (array_key_exists($name, $data)) {
                        $input[$name] = $data[$name];
                    }

                    continue;
                }

                try {
                    $input[$name] = DataProvider::getInstance($dataProviderKey, $index)->get($name);
                } catch (\Exception $e) {

                }
            }

            if (!array_key_exists($name, $input) || $input[$name] === null) {
                $input[$name] = $default;
            }

            $input[$name] = self::getParam($name, $input[$name], $param);
        }

        return $input;
    }

    /**
     * Return valid input value
     *
     * @param  $name
     * @param  $value
     * @param  $param
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    private static function getParam($name, $value, $param)
    {
        if (empty($param)) {
            return $value;
        }

        if ($value === null && array_key_exists('default', $param)) { // todo: выпилить - теперь есть фильтр DefaultValue
            $value = $param['default'];
        }

        if (isset($param['type'])) {
            $value = Php::castTo($param['type'], $value);
        }

        if (isset($param['filters'])) {
            /**
             * @var Filter $filterClass
             * @var array $filterOptions
             */
            foreach ((array)$param['filters'] as $filterClass => $filterOptions) {
                if (is_int($filterClass)) {
                    $filterClass = $filterOptions;
                    $filterOptions = [];
                }

                if (is_callable($filterClass)) {
                    $value = $filterClass([$name => $value], $name, (array)$filterOptions);
                } else {
                    $filterClass = Filter::getClass($filterClass);

                    $value = $filterClass::getInstance()->filter([$name => $value], $name, (array)$filterOptions);
                }
            }
        }

        //        if (isset($param['converter']) && is_callable($param['converter'])) {
        //            $value = $param['converter']($value);
        //        }

        return $value;
    }
}