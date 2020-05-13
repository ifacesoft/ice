<?php

namespace Ice\Core;

class Widget_Scope extends Container
{
    use Stored;

    protected static function getDefaultKey()
    {
        return 'default';
    }
}