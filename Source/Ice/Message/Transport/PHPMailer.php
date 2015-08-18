<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 17.08.15
 * Time: 18:11
 */

namespace Ice\Message\Transport;


use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Message;
use Ice\Core\Message_Transport;

class PHPMailer extends Message_Transport
{
    private $phpmailer = null;

    protected static function create($key)
    {
        $config = Config::getInstance(__CLASS__)->gets($key);

        /** @var PHPMailer $messageTransport */
        $messageTransport = parent::create($key);

        $messageTransport->phpmailer = new \PHPMailer();

        $messageTransport->phpmailer->isSMTP();
        $messageTransport->phpmailer->SMTPDebug = Environment::getInstance()->isDevelopment() ? 2 : 0;
        $messageTransport->phpmailer->Debugoutput = 'html';
//
//        $messageTransport->phpmailer
    }

    public function send(Message $message)
    {
        // TODO: Implement send() method.
    }
}