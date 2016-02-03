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

    protected function init(array $data)
    {
        parent::init($data);

        $key = $this->getInstanceKey();

        $config = Config::getInstance(__CLASS__);

        $this->phpMailer = new \PHPMailer();

        $this->phpMailer->CharSet = 'UTF-8';

        $this->phpMailer->isSMTP();

        $this->phpMailer->SMTPSecure = 'tls';
        $this->phpMailer->SMTPAuth = true;

        $this->phpMailer->SMTPDebug = $config->get($key . '/debug');
        $this->phpMailer->Debugoutput = 'html';

        $this->phpMailer->Host = $config->get($key . '/smtpHost');
        $this->phpMailer->Port = $config->get($key . '/smtpPort');

        $this->phpMailer->Username = $config->get($key . '/smtpUser');
        $this->phpMailer->Password = $config->get($key . '/smtpPass');

        $this->phpMailer->setFrom($this->getFromAddress(), $this->getFromName());
        $this->phpMailer->addReplyTo($this->getReplyToAddress(), $this->getReplyToName());

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