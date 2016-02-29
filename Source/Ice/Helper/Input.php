<?php

namespace Ice\Helper;

use Ice\Core\Action;
use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\DataProvider\Cli;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\DataProvider\Session;
use Ice\Exception\Error;
use Ice\Exception\Http_Not_Found;

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
                ? ($param['providers'] == '*' ? ['default', Request::class, Router::class, Cli::class, Session::class] : (array)$param['providers'])
                : ['default'];

            foreach ($dataProviderKeys as $dataProviderKey) {
                if (isset($input[$name])) {
                    break;
                }

                if ($dataProviderKey == 'default') {
                    $input[$name] = array_key_exists($name, $data) ? $data[$name] : null;
                    continue;
                }

                try { // пока провайдер роутер понимает только нативные роуты (не понмает, например, от Symfony)
                    $input[$name] = DataProvider::getInstance($dataProviderKey)->get($name);
                } catch (Http_Not_Found $e) {
                    //
                }
            }

//            if (!isset($input[$name]) && (!isset($param['required']) || $param['required'] === true)) {
//                throw new Error('Param {$0} is required in input');
//            } else {
//                $input[$name] = null;
//            }

            $input[$name] =  Input::getParam($name, $input[$name], $param);
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

//        if (isset($param['validators'])) {
//            foreach ((array)$param['validators'] as $validatorClass => $validatorParams) {
//                if (is_int($validatorClass)) {
//                    $validatorClass = $validatorParams;
//                    $validatorParams = null;
//                }
//
//                Validator::validate($validatorClass, $validatorParams, $name, $value);
//            }
//        }

        //        if (isset($param['converter']) && is_callable($param['converter'])) {
        //            $value = $param['converter']($value);
        //        }

        return $value;
    }
}