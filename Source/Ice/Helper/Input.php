<?php

namespace Ice\Helper;

use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Filter;
use Ice\DataProvider\Cli;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\DataProvider\Session;

class Input
{


    /**
     * Gets all declared input data
     *
     * @param array $data Addition input data
     * @param array $configInput Declared input params
     * @return array
     */
    public static function get(array $configInput, array $data = [])
    {
        $configInput = array_merge($configInput, array_keys($data));

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
                ? ($param['providers'] == '*'
                    ? ['default', Request::class, Router::class, Cli::class, Session::class]
                    : (array)$param['providers']
                )
                : ['default'];

            foreach ($dataProviderKeys as $key => $dataProviderKey) {
                if (array_key_exists($name, $input)) {
                    break;
                }

                $index = DataProvider::DEFAULT_INDEX;

                if (is_string($key)) {
                    $index = $dataProviderKey;
                    $dataProviderKey = $key;
                }

                if ($dataProviderKey == 'default') {
                    if (array_key_exists($name, $data)) {
                        $input[$name] = $data[$name];
                    }

                    continue;
                }

                try { // пока провайдер роутер понимает только нативные роуты (не понмает, например, от Symfony)
                    $input[$name] = DataProvider::getInstance($dataProviderKey, $index)->get($name, null, true);
                } catch (Exception $e) {
                }
            }

            if (!array_key_exists($name, $input)) {
                $input[$name] = null;
            }

            $input[$name] = Input::getParam($name, $input[$name], $param);
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

        if ($value === null && array_key_exists('default', $param)) {
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

                $value = $filterClass::getInstance()->filter([$name => $value], $name, (array)$filterOptions);
            }
        }

        //        if (isset($param['converter']) && is_callable($param['converter'])) {
        //            $value = $param['converter']($value);
        //        }

        return $value;
    }
}