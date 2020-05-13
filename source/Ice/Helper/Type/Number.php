<?php

namespace Ice\Helper;

class Type_Number
{
    /**
     * Return true for each $number call
     *
     * @param $number
     * @return bool
     */
    public static function isRandomEachNumber($number)
    {
        $number = (int) $number;

        if (!$number && $number < 0) {
            return false;
        }

        if ($number == 1) {
            return true;
        }

        return rand(1, $number) == 1;
    }
}