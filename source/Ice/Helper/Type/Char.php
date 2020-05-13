<?php

namespace Ice\Helper;

class Type_Char
{
    public static function isUpperCase($char) {
        return mb_strtoupper($char) == $char;
    }
}