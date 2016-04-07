<?php

namespace Ice\Core;

abstract class Query_Scope extends Container
{
    use Stored;

    protected static function getDefaultKey()
    {
        return 'default';
    }
}