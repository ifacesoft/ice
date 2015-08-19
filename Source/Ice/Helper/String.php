<?php
/**
 * Ice helper string class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
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
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class String
{
    const TRIM_TYPE_BOTH = 'both';
    const TRIM_TYPE_LEFT = 'left';
    const TRIM_TYPE_RIGHT = 'right';

    /**
     * Trim with some chars
     *
     * @param  $string
     * @param  null $chars
     * @param  string $type
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since   0.0
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

    /**
     * Check starts with string
     *
     * @param  $haystack
     * @param  $needles
     * @param  string $type
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function startsWith($haystack, $needles, $type = 'or')
    {
        $isStartWith = false;

        foreach ((array)$needles as $needle) {
            $length = strlen($needle);
            $isStartWith = substr($haystack, 0, $length) === $needle;

            if ($type == 'or' && $isStartWith == true) {
                return $isStartWith;
            }

            if ($type == 'and' && $isStartWith == false) {
                return $isStartWith;
            }
        }

        return $isStartWith;
    }

    /**
     * Check ends with string
     *
     * @param  $haystack
     * @param  $needle
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Return random string
     *
     * @param  int $length
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRandomString($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function properText($text){
        $text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
        $text = preg_replace('~^(&([a-zA-Z0-9]);)~',htmlentities('${1}'),$text);
        return($text);
    }

}
