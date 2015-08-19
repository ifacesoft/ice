<?php

namespace Ice\Message\Transport;

use Ice\Core\Config;
use Ice\Core\Message;
use Ice\Core\Message_Transport;
use Ice\Message\Mail;

class PHPMailer extends Message_Transport
{
    /** @var \PHPMailer */
    private $phpMailer = null;

    protected static function create($key)
    {
        $config = Config::getInstance(__CLASS__);

        /** @var PHPMailer $messageTransport */
        $messageTransport = parent::create($key);

        $messageTransport->phpMailer = new \PHPMailer();

        $messageTransport->phpMailer->CharSet = 'UTF-8';

        $messageTransport->phpMailer->isSMTP();

        $messageTransport->phpMailer->SMTPSecure = 'tls';
        $messageTransport->phpMailer->SMTPAuth = true;

        $messageTransport->phpMailer->SMTPDebug = $config->get($key . '/debug');
        $messageTransport->phpMailer->Debugoutput = 'html';

        $messageTransport->phpMailer->Host = $config->get($key . '/smtpHost');
        $messageTransport->phpMailer->Port = $config->get($key . '/smtpPort');

        $messageTransport->phpMailer->Username = $config->get($key . '/smtpUser');
        $messageTransport->phpMailer->Password = $config->get($key . '/smtpPass');

        $messageTransport->phpMailer->setFrom($messageTransport->getFromAddress(), $messageTransport->getFromName());
        $messageTransport->phpMailer->addReplyTo($messageTransport->getReplyToAddress(), $messageTransport->getReplyToName());

        $messageTransport->phpMailer->isHTML(true);

        return $messageTransport;
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
            PHPMailer::getLogger()->exception($phpMailer->ErrorInfo, __FILE__, __LINE__);
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