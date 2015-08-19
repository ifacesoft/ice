<?php

namespace Ice\Core;

use Ebs\Model\Log_Message;
use Ice\Core;
use Ice\Helper\Date;

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
     * Carbon copy addresses
     *
     * @var array|string|null
     */
    private $cc = null;

    /**
     * Blind carbon copy
     *
     * @var array|string|null
     */
    private $bcc = null;

    /**
     * @return array
     */
    public function getCc()
    {
        return (array)$this->cc;
    }

    /**
     * @param array|string $cc
     * @return Message
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
        return $this;
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return (array)$this->bcc;
    }

    /**
     * @param array|string $bcc
     * @return Message
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

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

    /**
     * @param Message_Transport|string|null $messageTransport
     */
    public function send($messageTransport = null)
    {
        $messageTransport = Message_Transport::getInstance($messageTransport);

        $logger = $messageTransport->getLogger();

        $recipients = $this->getRecipients();

        $address = key($recipients);

        if (is_int($address)) {
            $address = array_shift($recipients);
            $name = '';
        } else {
            $name = array_shift($recipients);
        }

        $log = Log_Message::create([
            'message_class' => get_class($this),
            'from_address' => $messageTransport->getFromAddress(),
            'from_name' => $messageTransport->getFromName(),
            'address' => $address,
            'name' => $name,
            'subject' => $this->getSubject(),
            'body' => $this->getBody(),
            'to' => $recipients,
            'cc' => $this->getCc(),
            'bcc' => $this->getBcc(),
            'recipient_count' => 1 + count($recipients) + count($this->getCc()) + count($this->getBcc())
        ]);

        $logger->save($log);

        $messageTransport->send($this);

        $log->set(['success_time' => Date::get()])->save();
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
     * @return array
     */
    public function getRecipients()
    {
        return (array)$this->recipients;
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