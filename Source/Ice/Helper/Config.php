<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 11/13/14
 * Time: 5:59 PM
 */

namespace Ice\Helper;

use Ice\Core\Logger;

class Config
{
    /**
     * Get more then one params of config
     *
     * @param array $config
     * @param $key
     * @param bool $isRequired
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function gets(array $config, $key = null, $isRequired = true)
    {
        if (empty($key)) {
            return $config;
        }

        $params = Config::isSetKey($config, $key);

        if ($params === false) {
            if ($isRequired) {
                Logger::getInstance(__CLASS__)->fatal(['Could not found required param {$0}', $key], __FILE__, __LINE__);
            }

            return [];
        }

        return (array)$params;
    }

    /**
     * Check is set key in config
     *
     * @param array $config
     * @param $key
     * @return array|bool
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    private static function isSetKey(array $config, $key)
    {
        $params = $config;

        foreach (explode('/', $key) as $keyPart) {
            if (!isset($params[$keyPart])) {
                return false;
            }

            $params = $params[$keyPart];
        }

        return $params;
    }
}