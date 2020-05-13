<?php

namespace Ice\Core;

use Ice\Exception\Error;

abstract class Query_Scope extends Container
{
    const FIELD_NAMES = 'fieldNames';

    use Stored;

    protected static function getDefaultKey()
    {
        return 'default';
    }

    protected function getFieldNames($data, $tableAlias)
    {
        return isset($data[Query_Scope::FIELD_NAMES][$tableAlias])
            ? $data[Query_Scope::FIELD_NAMES][$tableAlias]
            : null;
    }
}