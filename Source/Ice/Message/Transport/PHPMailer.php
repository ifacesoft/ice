<?php

namespace Ice\Message\Transport;

use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Environment;
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

        $messageTransport->phpMailer->setFrom($config->get($key . '/fromAddress'), $config->get($key . '/fromName'));

        if ($replyTo = $config->gets($key . '/replyTo')) {
            PHPMailer::replyTo($messageTransport->phpMailer, $replyTo);
        }

        if ($cc = $config->gets($key . '/cc')) {
            PHPMailer::cc($messageTransport->phpMailer, $cc);
        }

        if ($bcc = $config->gets($key . '/bcc')) {
            PHPMailer::bcc($messageTransport->phpMailer, $bcc);
        }

        $messageTransport->phpMailer->isHTML(true);

        return $messageTransport;
    }

    /**
     * @param Message|Mail $message
     */
    public function send(Message $message)
    {
        $phpMailer = clone $this->phpMailer;

        foreach ((array) $message->getRecipients() as $address => $name) {
            if (empty($name)) {
                continue;
            }

            if (is_int($address)) {
                $phpMailer->addAddress($name);
            } else {
                $phpMailer->addAddress($address, $name);
            }
        }

        if ($replyTo = $message->getReplyTo()) {
            PHPMailer::replyTo($phpMailer, $replyTo);
        }

        if ($replyTo = $message->getCc()) {
            PHPMailer::cc($phpMailer, $replyTo);
        }
        if ($replyTo = $message->getBcc()) {
            PHPMailer::bcc($phpMailer, $replyTo);
        }

        $phpMailer->Subject = $message->getSubject();

        $phpMailer->msgHTML($message->getBody());

        if (!$phpMailer->send()) {
            PHPMailer::getLogger()->exception($phpMailer->ErrorInfo, __FILE__, __LINE__);
        }
    }

    private static function replyTo(\PHPMailer $phpMailer, $value)
    {
        foreach ((array)$value as $address => $name) {
            if (empty($name)) {
                continue;
            }

            if (is_int($address)) {
                $phpMailer->addReplyTo($name);
            } else {
                $phpMailer->addReplyTo($address, $name);
            }
        }
    }

    private static function cc(\PHPMailer $phpMailer, $value)
    {
        foreach ((array)$value as $address => $name) {
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

    private static function bcc(\PHPMailer $phpMailer, $value)
    {
        foreach ((array)$value as $address => $name) {
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