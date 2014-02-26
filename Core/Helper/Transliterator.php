<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.02.14
 * Time: 0:31
 */

namespace ice\core\helper;


class Transliterator
{
    public static function transliterate($string)
    {
        $string = transliterator_transliterate(
            "Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();",
            $string
        );
        $string = preg_replace('/[-\s]+/', '_', $string);
        return trim($string, '_');
    }
} 