<?php
/**
 * Ice helper transliterator class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Transliterator
 *
 * Helper for transliteration
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Transliterator
{
    /**
     * Transliterate string
     *
     * @param  $string
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function transliterate($string)
    {
        $string = \transliterator_transliterate(
            "Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; Lower();",
            $string
        );

        $string = str_replace('ʹ', '', preg_replace('/[_\s]+/', '_', $string));
        return trim($string, '_');
    }
}
