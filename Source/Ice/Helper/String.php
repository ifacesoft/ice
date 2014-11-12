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
     * @version 0.0
     * @since 0.0
     */
    public static function trim($string, $chars = null, $type = self::TRIM_TYPE_BOTH)
    {
        $chars = (array)$chars;

        if (empty($chars)) {
            switch ($type) {
                case self::TRIM_TYPE_BOTH:
                    return trim($string);
                case self::TRIM_TYPE_LEFT:
                    return ltrim($string);
                case self::TRIM_TYPE_RIGHT:
                    return rtrim($string);
                default:
                    return trim($string);
            }
        } else {
            foreach ($chars as $signs) {
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
        }

        throw new Exception('wtf!');
    }
} 