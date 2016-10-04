<?php

namespace Ice\MessageTransport;

use Ice\Core\Config;
use Ice\Core\Message;
use Ice\Core\MessageTransport;
use Ice\Message\Mail;

class PHPMailer extends MessageTransport
{
    /** @var \PHPMailer */
    private $phpMailer = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $key = $this->getInstanceKey();

        $config = Config::getInstance(__CLASS__);

        $this->phpMailer = new \PHPMailer();

        $this->phpMailer->CharSet = 'UTF-8';

        $this->phpMailer->isSMTP();

        $this->phpMailer->SMTPDebug = $config->get($key . '/debug');
        $this->phpMailer->Debugoutput = 'html';

        $this->phpMailer->Host = $config->get($key . '/smtpHost');
        $this->phpMailer->Port = $config->get($key . '/smtpPort');

        if ($username = $config->get($key . '/smtpUser')) {
            $this->phpMailer->SMTPAuth = true;
            $this->phpMailer->SMTPSecure = 'tls';
            $this->phpMailer->Username = $username;
            $this->phpMailer->Password = $config->get($key . '/smtpPass');
        } else {
            $this->phpMailer->SMTPAuth = false;
            $this->phpMailer->SMTPSecure = false;
        }

        $this->phpMailer->setFrom($this->getFromAddress(), $this->getFromName());

        if ($replyToAddress = $this->getReplyToAddress()) {
            $this->phpMailer->addReplyTo($this->getReplyToAddress(), $this->getReplyToName());
        }

        $this->phpMailer->isHTML(true);
    }

    /**
     * @param Message|Mail $message
     */
    public function send(Message $message)
    {
        $phpMailer = clone $this->phpMailer;

        $phpMailer->Subject = $message->getSubject();

        $phpMailer->msgHTML($message->getBody());

        PHPMailer::to($phpMailer, $message->getRecipients());
        PHPMailer::cc($phpMailer, $message->getCc());
        PHPMailer::bcc($phpMailer, $message->getBcc());

        foreach ($message->getAttachments() as $name => $attachment) {
            if (is_string($attachment)) {
                $phpMailer->addStringAttachment($attachment, $name);
            } else {
                $phpMailer->addAttachment($attachment['path'], $name, $attachment['encoding'], $attachment['type']);
            }
        }

        if (!$phpMailer->send()) {
            $this->getLogger()->exception($phpMailer->ErrorInfo, __FILE__, __LINE__);
        }
    }

    private static function to(\PHPMailer $phpMailer, array $value)
    {
        foreach ($value as $address => $name) {
            if (empty($name)) {
                continue;
            }

            if (is_int($address)) {
                $phpMailer->addAddress($name);
            } else {
                $phpMailer->addAddress($address, $name);
            }
        }
    }

    private static function cc(\PHPMailer $phpMailer, array $value)
    {
        foreach ($value as $address => $name) {
            if (empty($name)) {
                continue;
            }

            if (is_int($address)) {
                $phpMailer->addCC($name);
            } else {
                $phpMailer->addCC($address, $name);
            }
        }
    }

    private static function bcc(\PHPMailer $phpMailer, array $value)
    {
        foreach ($value as $address => $name) {
            if (empty($name)) {
                continue;
            }

            if (is_int($address)) {
                $phpMailer->addBCC($name);
            } else {
                $phpMailer->addBCC($address, $name);
            }
        }
    }
}