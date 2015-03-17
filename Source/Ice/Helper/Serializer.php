<?php
/**
 * Ice helper serializer class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;

/**
 * Class Serializer
 *
 * Helper for serialize
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Serializer
{
    const SERIALIZER_DEFAULT = 'default';
    const SERIALIZER_JSON = 'json';
    const SERIALIZER_IGBINARY = 'igbinary';

    /**
     * Serialize with known serializer
     *
     * @param mixed $data
     * @param string $serializer
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function serialize($data, $serializer = null)
    {
        switch (self::getSerializer($serializer)) {
            case self::SERIALIZER_DEFAULT:
                return serialize($data);
            case self::SERIALIZER_JSON:
                return Json::encode($data);
            case self::SERIALIZER_IGBINARY:
                return igbinary_serialize($data);
            default:
                Core_Logger::getInstance(__CLASS__)->exception(['Unknown serializer {$0}', $serializer], __FILE__, __LINE__);
        }
    }

    /**
     * Get current serializer
     *
     * @param string $serializer
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getSerializer($serializer = null)
    {
        if ($serializer !== null) {
            return $serializer;
        }

        return self::SERIALIZER_JSON;

        return function_exists('igbinary_serialize')
            ? self::SERIALIZER_IGBINARY
            : self::SERIALIZER_DEFAULT;
    }

    /**
     * Unserialize with known serializer
     *
     * @param mixed $data
     * @param string $serializer
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function unserialize($data, $serializer = null)
    {
        switch (self::getSerializer($serializer)) {
            case self::SERIALIZER_DEFAULT:
                return unserialize($data);
            case self::SERIALIZER_JSON:
                return Json::decode($data);
            case self::SERIALIZER_IGBINARY:
                return igbinary_unserialize($data);
            default:
                Core_Logger::getInstance(__CLASS__)->exception(['Unknown serializer {$0}', $serializer], __FILE__, __LINE__);
        }
    }
} 