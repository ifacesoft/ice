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
use Exception;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;

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
    const ZERO = '0000-01-01 00:00:00';
    const START = '1970-01-01 00:00:00';
    const FUTURE = '2099-12-31 23:59:59';
    const FORMAT_MYSQL_DATE = 'Y-m-d';
    const FORMAT_DATE = 'd.m.y';
    const FORMAT_DATETIME = 'd.m.y H:i:s';

    public static function getMonthMap($lang = 'ru')
    {
        return [
            'Январь' => '01',
            'Февраль' => '02',
            'Март' => '03',
            'Апрель' => '04',
            'Май' => '05',
            'Июнь' => '06',
            'Июль' => '07',
            'Август' => '08',
            'Сентябрь' => '09',
            'Октябрь' => '10',
            'Ноябрь' => '11',
            'Декабрь' => '12'
        ];
    }

    public static function getMonth($date, $type, $lang = 'ru')
    {
        $monthAr = [
            '01' => ['ru' => ['Январь', 'Января']],
            '02' => ['ru' => ['Февраль', 'Февраля']],
            '03' => ['ru' => ['Март', 'Марта']],
            '04' => ['ru' => ['Апрель', 'Апреля']],
            '05' => ['ru' => ['Май', 'Мая']],
            '06' => ['ru' => ['Июнь', 'Июня']],
            '07' => ['ru' => ['Июль', 'Июля']],
            '08' => ['ru' => ['Август', 'Августа']],
            '09' => ['ru' => ['Сентябрь', 'Сентября']],
            '10' => ['ru' => ['Октябрь', 'Октября']],
            '11' => ['ru' => ['Ноябрь', 'Ноября']],
            '12' => ['ru' => ['Декабрь', 'Декабря']],
        ];

        return $monthAr[date('m', $date)][$lang][$type];
    }

    /**
     * Return revision by current time
     *
     * @return string
     * @throws Exception
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
     * @param $time
     * @param $fromTimezone
     * @param $toTimezone
     * @return string
     * @throws Exception
     */
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
     * @throws Exception
     * @version 1.4
     * @since   0.0
     * @deprecated Use self::getDateTime or self::getTimestampDateTime
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function get($serverDataTime = null, $format = null, $clientTimezone = true, $reverse = false)
    {
        if (empty($serverDataTime)) {
            $serverDataTime = 'now';
        }

        if (empty($format)) {
            $format = self::FORMAT_MYSQL;
        }

        $serverTimezone = self::getServerTimezone();

        $date = new DateTime(
            date(self::FORMAT_MYSQL, is_int($serverDataTime) ? $serverDataTime : strtotime($serverDataTime)),
            new DateTimeZone($serverTimezone)
        );

        if (empty($clientTimezone)) {
            return $date->format($format);
        }

        if ($clientTimezone === true) {
            $clientTimezone = self::getClientTimezone();
        }

        if ($clientTimezone === false) {
            $clientTimezone = self::getServerTimezone();
        }

        if ($clientTimezone == $serverTimezone) {
            return $date->format($format);
        }

        if ($reverse) {
            $date = new DateTime(
                date(self::FORMAT_MYSQL, is_int($serverDataTime) ? $serverDataTime : strtotime($serverDataTime)),
                new DateTimeZone($clientTimezone)
            );

            $clientTimezone = $serverTimezone;
        }

        $date->setTimezone(new DateTimeZone($clientTimezone));

        return $date->format($format);
    }

    /**
     * @param null $serverDataTime
     * @param null $format
     * @param bool $clientTimezone
     * @param bool $reverse
     * @return string
     * @throws Exception
     */
    public static function getDateTime($serverDataTime = null, $format = null, $clientTimezone = true, $reverse = false)
    {
        return self::get($serverDataTime, $format, $clientTimezone, $reverse);
    }

    /**
     * @param null $serverDataTime
     * @param null $format
     * @param bool $clientTimezone
     * @param bool $reverse
     * @return string
     * @throws Exception
     */
    public static function getTimestampDateTime($serverDataTime = null, $format = null, $clientTimezone = true, $reverse = false)
    {
        return self::get($serverDataTime, $format, $clientTimezone, $reverse);
    }

    /**
     * @return array|mixed|string
     * @throws \Ice\Core\Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public static function getClientTimezone()
    {
        return 'Europe/Moscow';

        // todo: Как-то избавиться от этого. (Ситуация в том, что в момент создания инстанса секуритьи мы делаем запрос на получение юзера, а там есть дата)
        return Security::$loaded
            ? Security::getInstance()->getUser()->getTimezone()
            : Module::getInstance()->getDefault('date')->get('client_timezone');
    }

    /**
     * @return array|string
     * @throws Config_Error
     * @throws FileNotFound
     * @throws \Ice\Core\Exception
     */
    public static function getServerTimezone()
    {
        return 'Europe/Moscow';

        return Module::getInstance()->getDefault('date')->get('server_timezone');
    }

    public static function getMonthName($time, $locale = 'ru_RU.UTF-8')
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
     * @param integer $checkTime - Checked time in seconds
     * @param integer $ttl - Time to live in seconds
     * @param integer $onTime - Check on timestamp in seconds
     * @return bool
     * @throws Exception
     */
    public static function expired($checkTime, $ttl = 0, $onTime = null)
    {
        $onTime = empty($onTime)
            ? strtotime(Date::get())
            : strtotime(Date::get($onTime));

        return $onTime - strtotime(self::get($checkTime)) >= $ttl;
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

    /**
     * @return array|string
     * @throws Config_Error
     * @throws FileNotFound
     * @throws \Ice\Core\Exception
     */
    public static function getFormat()
    {
        return Module::getInstance()->getDefault('date')->get('format');
    }

    /**
     * Return count days from any moment in the Universe to now
     * @param $dateSince
     * @return bool|\DateInterval
     * @throws Exception
     */
    public static function getCountDaysFromDate($dateSince)
    {
        $dateCurrent = new DateTime(self::getDateTime($dateSince, 'Y-m-d H:i:s'));

        return $dateCurrent->diff(new DateTime(self::getDateTime(null, 'Y-m-d H:i:s')))->days;
    }
}
