<?php

namespace Ice\Helper;

use Ice\Core\Action;
use Ice\Core\Configured;
use Ice\Core\Data_Provider;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Router as Data_Provider_Router;
use Ice\Data\Provider\Session as Data_Provider_Session;
use Ice\Data\Provider\Cli as Data_Provider_Cli;

class Input
{
    /**
     * @param Configured $class
     * @param array $data
     * @param array $params
     * @return array
     */
    public static function get($class, array $data = [], array $params = [])
    {
        $params = array_merge(array_keys($data), $class::getConfig()->gets('input', false), $params);

        $dataProviderKeyMap = [
            'request' => Data_Provider_Request::DEFAULT_DATA_PROVIDER_KEY,
            'router' => Data_Provider_Router::DEFAULT_DATA_PROVIDER_KEY,
            'session' => Data_Provider_Session::DEFAULT_DATA_PROVIDER_KEY,
            'cli' => Data_Provider_Cli::DEFAULT_DATA_PROVIDER_KEY,
        ];

        $input = [];

        foreach ($params as $name => $param) {
            if (is_int($name)) {
                $name = $param;
                $param = [];
            }

            if (!is_array($param)) {
                $input[$name] = $param;
                continue;
            }

            $dataProviderKeys = isset($param['providers'])
                ? (array)$param['providers']
                : ['default'];

            foreach ($dataProviderKeys as $dataProviderKey) {
                if (isset($input[$name])) {
                    break;
                }

                if (isset($dataProviderKeyMap[$dataProviderKey])) {
                    $dataProviderKey = $dataProviderKeyMap[$dataProviderKey];
                }

                if ($dataProviderKey == 'default') {
                    $input[$name] = array_key_exists($name, $data) ? $data[$name] : null;
                    continue;
                }

                $input[$name] = Data_Provider::getInstance($dataProviderKey)->get($name);
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

        if ($value === null && isset($param['default'])) {
            $value = $param['default'];
        }

        if (isset($param['type'])) {
            $value = Php::castTo($param['type'], $value);
        }

        if (isset($param['validators'])) {
            foreach ((array)$param['validators'] as $validatorClass => $validatorParams) {
                if (is_int($validatorClass)) {
                    $validatorClass = $validatorParams;
                    $validatorParams = null;
                }

                Validator::validate($validatorClass, $validatorParams, $name, $value);
            }
        }

        //        if (isset($param['converter']) && is_callable($param['converter'])) {
        //            $value = $param['converter']($value);
        //        }

        return $value;
    }
}