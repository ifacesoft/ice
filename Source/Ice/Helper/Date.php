<?php
/**
 * Ice helper date class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Locale;

/**
 * Class Date
 *
 * Helper for dates
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Date
{
    /**
     * 2001-03-10 17:16:18 (формат MySQL DATETIME)
     */
    const FORMAT_MYSQL = 'Y-m-d H:i:s';
    const FORMAT_REVISION = 'mdHi';
    const ZERO = '0000-00-00 00:00:00';
    const FUTURE = '2099-12-31 00:00:00';
    const FORMAT_MYSQL_DATE = 'Y-m-d';

//$UTC = new DateTimeZone("UTC");
//$newTZ = new DateTimeZone("America/New_York");
//$date = new DateTime( "2011-01-01 15:00:00", $UTC );
//$date->setTimezone( $newTZ );
//echo $date->format('Y-m-d H:i:s');

    /**
     * Return revision by current time
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRevision()
    {
        return Date::get(null, Date::FORMAT_REVISION);
    }

    /**
     * Return current data in default (mysql) format
     *
     * @param  null $time
     * @param  string $format
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function get($time = null, $format = Date::FORMAT_MYSQL)
    {
        return $time ? date($format, $time) : date($format);
    }

    public static function getMonth($time)
    {
        return Date::strftime('%B', $time, 'ru_RU.UTF-8');
    }

    private static function strftime($format, $time, $locale)
    {
        $defaultLocale = setlocale(LC_TIME, 0);
        setlocale(LC_TIME, $locale);
        $time = strftime($format, $time);
        setlocale(LC_TIME, $defaultLocale);
        return $time;
    }

    public static function getMonthShort($time)
    {
        return Date::strftime('%b', $time, 'ru_RU.UTF-8');
    }

    public static function getDayShort($time)
    {
        return Date::strftime('%a', $time, 'ru_RU.UTF-8');
    }

    public static function getDay($time)
    {
        return Date::strftime('%A', $time, 'ru_RU.UTF-8');
    }

    public static function expired($time, $ttl)
    {
        return time() - $time > $ttl;
    }

    public static function convertPHPToMomentFormat($format)
    {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz', // deprecated since version 1.6.0 of moment.js
            'I' => '', // no equivalent
            'O' => '', // no equivalent
            'P' => '', // no equivalent
            'T' => '', // no equivalent
            'Z' => '', // no equivalent
            'c' => '', // no equivalent
            'r' => '', // no equivalent
            'U' => 'X',
        ];
        $momentFormat = strtr($format, $replacements);
        return $momentFormat;
    }
}
