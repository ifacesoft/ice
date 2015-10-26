<?php

namespace Ice\Core;

abstract class Message_Transport extends Container
{
    use Stored;

    private static $defaultClassKey = 'Ice\Message\Transport\PHPMailer/default';
    private static $defaultKey = 'default';

    private $fromAddress = null;
    private $fromName = null;
    private $replyToAddress = null;
    private $replyToName = null;

    /**
     * @param string $key
     * @param int $ttl
     * @param array $params
     * @return Message_Transport
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   1.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
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

    protected function init(array $data)
    {
        $config = Config::getInstance(self::getClass());

        $key = $this->getInstanceKey();

        $this->fromAddress = $config->get($key . '/fromAddress');
        $this->fromName = $config->get($key . '/fromName', false);

        $this->replyToAddress = $config->get($key . '/replyToAddress');
        $this->replyToName = $config->get($key . '/replyToName', false);
    }
}