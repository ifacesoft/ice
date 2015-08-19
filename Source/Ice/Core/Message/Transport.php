<?php

namespace Ice\Core;

abstract class Message_Transport extends Container
{
    private static $defaultClassKey = 'Ice\Message\Transport\PHPMailer/default';
    private static $defaultKey = 'default';

    private $fromAddress = null;
    private $fromName = null;
    private $replyToAddress = null;
    private $replyToName = null;

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

        /** @var Message_Transport $messageTransport */
        $messageTransport = new $class();

        $config = Config::getInstance($class);

        $messageTransport->fromAddress = $config->get($key . '/fromAddress');
        $messageTransport->fromName = $config->get($key . '/fromName', false);

        $messageTransport->replyToAddress = $config->get($key . '/replyToAddress');
        $messageTransport->replyToName = $config->get($key . '/replyToName', false);

        return $messageTransport;
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

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getReplyToAddress()
    {
        return $this->replyToAddress;
    }

    /**
     * @return string
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }
}