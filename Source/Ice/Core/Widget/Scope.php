<?php

namespace Ice\Core;

class Widget_Scope extends Container
{
    use Stored;

    protected static function create($key)
    {
        $class = self::getClass();

        return new $class();
    }


    protected static function getDefaultKey()
    {
        return 'default';
    }
}