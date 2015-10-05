<?php

namespace Ice\Core;

abstract class Query_Scope extends Container
{
    use Stored;

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Init object
     *
     * @param array $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $params)
    {
        // TODO: Implement init() method.
    }
}