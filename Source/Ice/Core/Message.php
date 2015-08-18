<?php

namespace Ice\Core;

use Ice\Core;

abstract class Message
{
    use Core;

    private $address = null;
    private $subject = null;
    private $body = null;

    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => null, 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    public static function create()
    {
        $class = self::getClass();

        /** @var Message $message */
        $message = new $class();

        return $message;
    }

    public function send($messageTransport = null)
    {
        $messageTransport = Message_Transport::getInstance($messageTransport);

        $messageTransport->send($this);
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param array|string $address
     * @return Message
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
}