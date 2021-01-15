<?php

namespace Ice\Helper;

use Ice\Exception\Config_Param_NotFound;
use RuntimeException;

class Config
{
    /**
     * Get more then one params of config
     *
     * @param array $config
     * @param null $key
     * @param bool $isRequired_default todo: Подумать, как сделать понятнее
     * @return array
     * @throws Config_Param_NotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function gets(array $config, $key = null, $isRequired_default = true)
    {
        if (empty($key)) {
            return $config;
        }

        try {
            $params = $config;

            foreach (explode('/', $key) as $keyPart) {
                if (!array_key_exists($keyPart, $params)) {
                    throw new RuntimeException('Param ' . $key . ' not found'); // Именно не Config_Param_NotFound
                }

                $params = $params[$keyPart];
            }

            return (array)$params;
        } catch (\Exception $e) {
            if ($isRequired_default === true) {
                throw new Config_Param_NotFound(['Could not found required param {$0}', $key], $config, $e);
            }

            return (array)$isRequired_default;
        }
    }

    /**
     * Set or update config param
     *
     * @param  array $config
     * @param  $key
     * @param  $value
     * @param  bool $force
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function set(array &$config, $key, $value, $force = false)
    {
        $params = &$config;

        foreach (explode('/', $key) as $keyPart) {
            if (!isset($params)) {
                $params = [];
            }

            if (!isset($params[$keyPart])) {
                $params[$keyPart] = null;
            }

            $params = &$params[$keyPart];
        }

        if ($force || !isset($params)) {
            $params = $value;
        } else {
            $params = (array)$params;
            array_unshift($params, $value);
        }
    }

    /**
     * Remove config param
     *
     * @param  $config
     * @param  $key
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function remove(array &$config, $key)
    {
        $params = &$config;

        foreach (explode('/', $key) as $keyPart) {
            if (!isset($params)) {
                return;
            }

            if (!isset($params[$keyPart])) {
                return;
            }

            $params = &$params[$keyPart];
        }

        unset($params);
    }
}
