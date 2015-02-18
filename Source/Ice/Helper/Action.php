<?php namespace Ice\Helper;


class Action
{
    /**
     * Return valid input value
     *
     * @param $name
     * @param $value
     * @param $param
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getInputParam($name, $value, $param)
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

        return $value;
    }
}