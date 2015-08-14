<?php

namespace Ice\Core;

abstract class Message extends Container
{
    private static $defaultClassKey = null;

    /**
     * @param null $key
     * @param null $ttl
     * @return Message
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

    protected static function getDefaultClassKey()
    {
        return Message::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }
}