<?php
/**
 * Ice helper string class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;


use Ice\Core\Exception;

/**
 * Class String
 *
 * Helper for string operations
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class String
{
    const TRIM_TYPE_BOTH = 'both';
    const TRIM_TYPE_LEFT = 'left';
    const TRIM_TYPE_RIGHT = 'right';

    /**
     * Trim with some chars
     *
     * @param $string
     * @param null $chars
     * @param string $type
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.0
     */
    public static function trim($string, $chars, $type = self::TRIM_TYPE_BOTH)
    {
        if (empty($chars)) {
            return trim($string);
        }

        foreach ((array)$chars as $signs) {
            switch ($type) {
                case self::TRIM_TYPE_BOTH:
                    return trim($string, $signs);
                case self::TRIM_TYPE_LEFT:
                    return ltrim($string, $signs);
                case self::TRIM_TYPE_RIGHT:
                    return rtrim($string, $signs);
                default:
                    return trim($string, $signs);
            }
        }

        return $string;
    }

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
} 