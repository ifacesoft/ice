<?php

namespace Ice\Core;

abstract class Query_Scope extends Container
{
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