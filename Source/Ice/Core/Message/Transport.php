<?php

namespace Ice\Core;

abstract class Message_Transport extends Container
{
    private static $defaultClassKey = 'Ice\Message\Transport\PHPMailer/default';
    private static $defaultKey = 'default';

    /**
     * @param string $key
     * @param int $ttl
     * @return Message_Transport
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $class = self::getClass();

        return new $class();
    }

    protected static function getDefaultClassKey()
    {
        return Message_Transport::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return Message_Transport::$defaultKey;
    }

    abstract public function send(Message $message);
}