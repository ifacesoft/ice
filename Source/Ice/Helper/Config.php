<?php

namespace Ice\Helper;

use Ice\Core\Logger;
use Ice\Exception\Config_Param_NotFound;

class Config
{
    /**
     * Get more then one params of config
     *
     * @param  array $config
     * @param  $key
     * @param  bool $isRequired_default
     * @return array
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
                    throw new \Exception('Param ' . $key . ' not found');
                }

                $params = $params[$keyPart];
            }

            return (array)$params;
        } catch (\Exception $e) {
            if ($isRequired_default === true) {
                Logger::getInstance(__CLASS__)->exception(
                    ['Could not found required param {$0}', $key],
                    __FILE__,
                    __LINE__,
                    null,
                    $config,
                    -1,
                    Config_Param_NotFound::class
                );
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
