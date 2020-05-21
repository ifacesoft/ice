<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Model\Log_Message;
use Ice\Model\Message_Template;
use Ice\Render\Replace;

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

    /**
     * @var string
     */
    private $bodySignature = '';

    /**
     * @var string
     */
    private $rawBody = null;

    private $attachments = [];

    public static function create()
    {
        $class = self::getClass();

        /** @var Message $message */
        $message = new $class();

        return $message;
    }

    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => null, 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /**
     * @param MessageTransport|string|null $messageTransport
     * @return Log_Message
     * @throws Exception
     * @throws \Exception
     */
    public function send($messageTransport = null)
    {
        if (!(is_object($messageTransport) && $messageTransport instanceof MessageTransport)) {
            $messageTransport = MessageTransport::getInstance($messageTransport);
        }

        $logger = $messageTransport->getLogger();

        $recipients = $this->getRecipients();

        $address = key($recipients);

        if (is_int($address)) {
            $address = reset($recipients);
            $name = '';
        } else {
            $name = reset($recipients);
        }

        $cc = $this->getCc();
        $bcc = $this->getBcc();

        $subject = $this->getSubject();

        $log = Log_Message::create([
            'message_class' => get_class($this),
            'from_address' => $messageTransport->getFromAddress(),
            'from_name' => $messageTransport->getFromName(),
            'address' => $address,
            'name' => $name,
            'subject' => $subject ? $subject : 'empty subject',
            'body' => $this->getBody(),
            'to' => $recipients,
            'cc' => $cc,
            'bcc' => $bcc,
            'recipient_count' => count($recipients) + count($cc) + count($bcc)
        ]);

        $logger->save($log);

        $result = $messageTransport->send($this);

        if ($recipients != $result['to'] || $cc != $result['cc'] || $bcc != $result['bcc']) {
            $result['subject'] = '!' . $log->get('subject');
        }

        return $log->set($result)->save();
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
        return $this->body ? $this->body . $this->getBodySignature() : $this->rawBody;
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
     * @return string
     */
    public function getBodySignature()
    {
        return $this->bodySignature;
    }

    /**
     * @param string $bodySignature
     * @return Message
     */
    public function setBodySignature(string $bodySignature)
    {
        $this->bodySignature = $bodySignature;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * @param string $rawBody
     * @return Message
     */
    public function setRawBody($rawBody)
    {
        $this->rawBody = $rawBody;

        return $this;
    }

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
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     * @return $this
     */
    public function addAttachment($name, $attachment)
    {
        $this->attachments[$name] = $attachment;

        return $this;
    }

    public function setTemplate(Message_Template $messageTmplate, array $params)
    {
        return $this
            ->setSubject($messageTmplate->get('subject'))
            ->setBody(Replace::getInstance()->fetch($messageTmplate->get('body'), $params, null, Replace::TEMPLATE_TYPE_STRING));
    }
}