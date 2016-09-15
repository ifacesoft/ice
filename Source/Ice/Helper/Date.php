<?php
/**
 * Ice helper date class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use DateTime;
use DateTimeZone;
use Ice\Core\Debuger;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Exception\Error;

/**
 * Class Date
 *
 * Helper for dates
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Date
{
    /**
     * 2001-03-10 17:16:18 (формат MySQL DATETIME)
     */
    const FORMAT_MYSQL = 'Y-m-d H:i:s';
    const FORMAT_REVISION = 'mdHi';
//    const ZERO = '0000-00-00 00:00:00';
//    const FUTURE = '2099-12-31 00:00:00';
    const FORMAT_MYSQL_DATE = 'Y-m-d';

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

    public static function getZero() {
        return Date::get('0000-01-01 00:00:00');
    }

    public static function getFuture() {
        return Date::get('2999-12-31 00:00:00');
    }

    public static function getTimezoneFromTo($time, $fromTimezone, $toTimezone)
    {
        return Date::get(Date::get($time, null, $fromTimezone, true), null, $toTimezone);
    }

    /**
     * Return current data in default (mysql) format
     *
     * @param string|integer $serverDataTime
     * @param string $format
     * @param bool $clientTimezone
     * @param bool $reverse
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.4
     * @since   0.0
     */
    public static function get($serverDataTime = null, $format = null, $clientTimezone = true, $reverse = false)
    {
        if (empty($serverDataTime)) {
            $serverDataTime = 'now';
        }

        if (empty($format)) {
            $format = Date::FORMAT_MYSQL;
        }

        $serverTimezone = Date::getServerTimezone();

        $date = new DateTime(
            date(Date::FORMAT_MYSQL, is_int($serverDataTime) ? $serverDataTime : strtotime($serverDataTime)),
            new DateTimeZone($serverTimezone)
        );

        if (empty($clientTimezone)) {
            return $date->format($format);
        }

        if ($clientTimezone === true) {
            $clientTimezone = Date::getClientTimezone();
        }

        if ($clientTimezone == $serverTimezone) {
            return $date->format($format);
        }

        if ($reverse) {
            $date = new DateTime(
                date(Date::FORMAT_MYSQL, is_int($serverDataTime) ? $serverDataTime : strtotime($serverDataTime)),
                new DateTimeZone($clientTimezone)
            );

            $clientTimezone = $serverTimezone;
        }

        $date->setTimezone(new DateTimeZone($clientTimezone));

        return $date->format($format);
    }

    public static function getClientTimezone()
    {
        return Security::$loaded
            ? Security::getInstance()->getUser()->getTimezone()
            : Module::getInstance()->getDefault('date')->get('client_timezone');
    }

    public static function getServerTimezone()
    {
        return Module::getInstance()->getDefault('date')->get('server_timezone');
    }

    public static function getMonth($time, $locale = 'ru_RU.UTF-8')
    {
        return Date::strftime('%B', $time, $locale);
    }

    private static function strftime($format, $time, $locale)
    {
        $defaultLocale = setlocale(LC_TIME, 0);
        setlocale(LC_TIME, $locale);
        $time = strftime($format, $time);
        setlocale(LC_TIME, $defaultLocale);
        return $time;
    }

    public static function getMonthShort($time, $locale = 'ru_RU.UTF-8')
    {
        return Date::strftime('%b', $time, $locale);
    }

    public static function getDayShort($time, $locale = 'ru_RU.UTF-8')
    {
        return Date::strftime('%a', $time, $locale);
    }

    public static function getDay($time, $locale = 'ru_RU.UTF-8')
    {
        return Date::strftime('%A', $time, $locale);
    }

    /**
     * @param $checkTime - Checked time in seconds
     * @param $ttl - Time to live in seconds
     * @param $onTime - Check on timestamp in seconds
     * @return bool
     */
    public static function expired($checkTime, $ttl, $onTime = null)
    {
        return ($onTime === null ? time() : $onTime) - $checkTime > $ttl;
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

    public static function convertPHPToFakeMomentFormat($format)
    {
        $replacements = [
            'd' => 'dd',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'mm',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'yyyy',
            'y' => 'yy',
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

    public static function getFormat()
    {
        return Module::getInstance()->getDefault('date')->get('format');
    }
}
