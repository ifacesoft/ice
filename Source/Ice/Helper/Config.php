<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 11/13/14
 * Time: 5:59 PM
 */

namespace Ice\Helper;


class Config
{
    public static function get($value)
    {
        return is_array($value) ? reset($value) : $value;
    }

    public static function gets($value)
    {
        return is_array($value) ? $value : (array)$value;
    }
}