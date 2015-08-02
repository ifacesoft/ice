<?php

namespace Ice\Core;

abstract class Router extends Container
{
    private static $defaultClassKey = null;

    /**
     * @param string $key
     * @param int $ttl
     * @return Router
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $class = self::getClass();

        return new $class($key);
    }

    public function init() {
        if (Router::$defaultClassKey === null) {
            Router::$defaultClassKey = get_class($this);
            return;
        }

        Router::getLogger()->warning('Router already initialized', __FILE__, __LINE__);
    }

    protected static function getDefaultClassKey()
    {
        return Router::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    public abstract function getUrl($routeName, array $params = []);
}