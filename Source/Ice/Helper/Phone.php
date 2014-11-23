<?php
/**
 * Ice helper phone class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Phone
 *
 * Helper phone
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Phone
{
    /**
     * Parse phone number
     *
     * @param $number
     * @param bool $isOnlySigits
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function parse($number, $isOnlySigits = false)
    {
        $number = '+ 7' . preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $number);

        if ($isOnlySigits) {
            $number = preg_replace('/\D/', '', $number);
        }

        return $number;
    }
} 