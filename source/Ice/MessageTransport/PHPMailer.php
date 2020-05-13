<?php

namespace Ice\MessageTransport;

use Ice\Core\Config;
use Ice\Core\Environment;
use Ice\Core\Message;
use Ice\Core\MessageTransport;
use Ice\Exception\Error;
use Ice\Helper\Date;
use Ice\Message\Mail;
use PHPMailer\PHPMailer\PHPMailer as PHPMailer_PHPMailer ;

class PHPMailer extends MessageTransport
{
    /** @var PHPMailer_PHPMailer */
    private $phpMailer = null;

    /**
     * PHPMailer constructor.
     * @param array $data
     * @throws \Ice\Core\Exception
     * @throws \phpmailerException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $config = Environment::getInstance()->getConfig(MessageTransport::class . '/' . get_class($this) . '/' . $this->getInstanceKey());

        $this->phpMailer = new PHPMailer_PHPMailer(true);

        $this->phpMailer->CharSet = 'UTF-8';

        $this->phpMailer->AllowEmpty = true;
        
        $this->phpMailer->isSMTP();

//        $this->phpMailer->isMail();

        $this->phpMailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $this->phpMailer->SMTPDebug = $config->get('debug');
        $this->phpMailer->Debugoutput = 'html';

        $this->setHost($config->get('smtpHost'));
        $this->setPort($config->get('smtpPort'));

        if ($username = $config->get('smtpUser')) {
            $this->phpMailer->SMTPAuth = true;
            $this->phpMailer->SMTPSecure = $config->get('smtpSecure');
            $this->phpMailer->Username = $username;
            $this->phpMailer->Password = $config->get('smtpPass');
        } else {
            $this->phpMailer->SMTPAuth = false;
            $this->phpMailer->SMTPSecure = false;
        }

        if ($replyToAddress = $this->getReplyToAddress()) {
            $this->phpMailer->addReplyTo($this->getReplyToAddress(), $this->getReplyToName());
        }

        $this->phpMailer->isHTML(true);
    }

    /**
     * @param Message|Mail $message
     * @param null $to
     * @param null $cc
     * @param null $bcc
     * @return array
     * @throws \Ice\Core\Exception
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Ice\Exception\Config_Error
     * @throws \phpmailerException
     */
    public function send(Message $message)
    {
        $phpMailer = clone $this->phpMailer;

        $phpMailer->Subject = $message->getSubject();

        if ($rawBody = $message->getRawBody()) {
            $phpMailer->setRawBody($rawBody);
        } else {
            $phpMailer->msgHTML($message->getBody());
        }
        
        $phpMailer->setFrom($this->getFromAddress(), $this->getFromName());

        $recipients = $this->checkRedirectRecipients($message->getRecipients());
        $cc = $this->checkRedirectRecipients($message->getCc());
        $bcc = $this->checkRedirectRecipients($message->getBcc());

        PHPMailer::to($phpMailer, $recipients);
        PHPMailer::cc($phpMailer, $cc);
        PHPMailer::bcc($phpMailer, $bcc);

        foreach ($message->getAttachments() as $name => $attachment) {
            if (is_string($attachment)) {
                $phpMailer->addStringAttachment($attachment, $name);
            } else {
                $phpMailer->addAttachment($attachment['path'], $name, $attachment['encoding'], $attachment['type']);
            }
        }

        $output = [
            'to' => $recipients,
            'cc' => $cc,
            'bcc' => $bcc
        ];

        try {
            if (!$phpMailer->send()) {
                throw new Error($phpMailer->ErrorInfo);
            }

            $output['success_time'] = Date::get();
        } catch (\Exception $e) {
            $this->getLogger()->error('Message not sent', __FILE__, __LINE__, $e);
        }

        return $output;
    }

    private static function to(PHPMailer_PHPMailer $phpMailer, array $value)
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

    private static function cc(PHPMailer_PHPMailer $phpMailer, array $value)
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

    private static function bcc(PHPMailer_PHPMailer $phpMailer, array $value)
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

    public function setHost($host)
    {
        $this->phpMailer->Host = $host;
    }

    public function setPort($port)
    {
        $this->phpMailer->Port = $port;
    }
}