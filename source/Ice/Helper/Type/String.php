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
use Ifacesoft\Ice\Core\Domain\Value\StringValue;

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
class Type_String
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
     *
     * @deprecated 1.21
     * @uses StringValue::create($haystack)->startsWith($needles, $type)
     * @removed 2.0
     */
    public static function startsWith($haystack, $needles, $type = 'or')
    {
        return StringValue::create($haystack)->startsWith($needles, $type);
    }

    /**
     * Check ends with string
     *
     * @param  $haystack
     * @param  $needles
     * @param string $type
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.5
     *
     * @deprecated 1.21
     * @uses StringValue::create($haystack)->endsWith($needles, $type)
     * @removed 2.0
     */
    public static function endsWith($haystack, $needles, $type = 'or')
    {
       return StringValue::create($haystack)->endsWith($needles, $type);
    }

    /**
     * Return random string
     *
     * @param int $length
     * @param array $blocks
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * A possible way to generate a random salt is by running the following command from a unix shell:
     * tr -c -d '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
     *
     * @version 1.1
     * @since   0.5
     */
    public static function getRandomString($length = 12, $blocks = [0, 1, 2])
    {
        $chArr = [
            0 => '0123456789',
            1 => 'abcdefghijklmnopqrstuvwxyz',
            2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        ];

        $characters = '';

        foreach ((array)$blocks as $block) {
            $characters .= $chArr[$block];
        }

        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function properText($text)
    {
        $text = \mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
        $text = preg_replace('~^(&([a-zA-Z0-9]);)~', htmlentities('${1}'), $text);
        return ($text);
    }

    public static function truncate($string, $length = 100, $append = '...')
    {
        return StringValue::create($string)->truncate($length, $append);
    }

    public static function substrpos($haystack, $needle, $offset = 0, $numOffset = 0)
    {
        return substr($haystack, $offset, Type_String::strpos($haystack, $needle, $offset, $numOffset));
    }

    public static function strpos($haystack, $needle, $offset = 0, $numOffset = 0)
    {
        $pos = $offset;

        for ($i = 0; $i < $numOffset; $i++) {
            $pos = \mb_strpos($haystack, $needle, $pos + 1);
        }

        return $pos === false ? \mb_strlen($haystack) : $pos;
    }

    public static function replaceMultiplyWhitespases($string, $replacement = ' ') {
        return StringValue::create($string)->replaceMultipleWhitespaces($replacement);
    }

    public static function replaceControlCharacters($string, $replacement = ' ') {
        return Type_String::replaceMultiplyWhitespases(preg_replace('/[\x00-\x1F\x7F ]/', $replacement, $string));
    }

    public static function replaceUtf8mb4Characters($string) {
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $string);
    }
    
    public static function removeSpecChars($string, $patten = '/[^\w ]+/u') {
        return preg_replace($patten, '', $string);
    }

    /**
     * Convert string to camelCase
     *
     * @param string $string
     * @param bool $lcfirst
     * @return string
     */
    public function toCamelCase($string, $lcfirst = true)
    {
        $string = str_replace(' ', '', ucwords(strtolower(preg_replace('/[-_]/', ' ', $string))));
        return ($lcfirst) ? lcfirst($string) : $string;
    }

    /**
     * Convert string to under_score
     *
     * @param string $word
     * @return string
     */
    public function toUnderScore($word)
    {
        $word = trim($word);
        $word = preg_replace('/[^a-zA-Z0-9\-\_\s]/', '', $word);
        $word = preg_replace('/[\_\s\-]+/', '_', $word);
        $word = preg_replace('/([a-z])([A-Z])/', '\\1_\\2', $word);
        $word = strtolower($word);
        return $word;
    }

    public static function printR($var, $whitespaces = true) {
        $string = str_replace('Array (', '(', preg_replace('/\s{2,}/', ' ', preg_replace('/[\x00-\x1F\x7F ]/', ' ', print_r($var, true))));

        return $whitespaces ? $string : str_replace(' ', '', $string);
    }
}
