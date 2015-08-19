<?php

namespace Ice\Core;

use Ice\Core;

abstract class Message
{
    use Core;

    /**
     * Recipients
     *
     * ```php
     * $recipients = 'admin@iceframework.net';
     * // or
     * $recipients = ['admin@iceframework.net' => 'Admin'];
     * // or
     * $recipients = ['admin@iceframework.net' => 'Admin', 'info@iceframework.net' => 'Info'];
     * ```
     *
     * @var array|string
     */
    private $recipients = null;

    /**
     * Message subject
     *
     * @var string
     */
    private $subject = null;

    /**
     * Message body
     *
     * @var string
     */
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

    /**
     * @return array|string
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array|string $recipients
     * @return Message
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
        return $this;
    }
}