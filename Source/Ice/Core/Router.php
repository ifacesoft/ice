<?php

namespace Ice\Core;

use Ice\Core;

abstract class Router extends Container
{
    use Core;

    /**
     * @param string $key
     * @param int $ttl
     * @param array $params
     * @return Router
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    public abstract function getUrl($routeName = null, array $params = [], $withGet = false);

    public abstract function getName($url = null, $method = null);

    public abstract function getParams();

    protected function init(array $data)
    {
    }
}