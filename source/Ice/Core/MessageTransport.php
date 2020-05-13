<?php

namespace Ice\Core;

abstract class MessageTransport extends Container
{
    use Stored;

    private static $defaultKey = 'default';

    private $fromAddress = null;
    private $fromName = null;
    private $replyToAddress = null;
    private $replyToName = null;

    /**
     * MessageTransport constructor.
     * @param array $data
     * @throws Exception
     */
    protected function __construct(array $data)
    {
        parent::__construct($data);

        $config = Environment::getInstance()->getConfig(__CLASS__ . '/' . get_class($this) . '/' . $this->getInstanceKey());

        $this->setFromAddress($config->get('fromAddress'));
        $this->setFromName($config->get('fromName', false));

        $this->setReplyToAddress($config->get('replyToAddress', ''));
        $this->setReplyToName($config->get('replyToName', false));
    }

    abstract public function setHost($host);
    abstract public function setPort($port);

    public function setFromAddress($fromAddress) {
        $this->fromAddress = $fromAddress;
    }

    public function setFromName($fromName) {
        $this->fromName = $fromName;
    }

    public function setReplyToAddress($replyToAddress) {
        $this->replyToAddress = $replyToAddress;
    }

    public function setReplyToName($replyToName) {
        $this->replyToName = $replyToName;
    }

    /**
     * @param array $addresses
     * @return array
     * @throws \Ice\Exception\Config_Error
     */
    protected function checkRedirectRecipients(array $addresses)
    {
        $recipients = [];

        $config = Environment::getInstance()->getConfig(__CLASS__ . '/' . get_class($this) . '/' . $this->getInstanceKey());

        $redirects = $config->gets('redirectTo');

        foreach ($addresses as $address => $name) {
            $checkAddress = is_int($address) ? $name : $address;
            $checkName = is_int($address) ? '' : $name;

            foreach ($redirects as $pattern => $redirect) {
                if (preg_match($pattern, $checkAddress)) {
                    foreach ((array)$redirect as $redirectTo) {
                        $recipients[$redirectTo] = '-> ' . $checkAddress . ' - ' . $checkName;
                    }
                    unset($redirectTo);

                    continue 2;
                }
            }

            $devAddresses = $config->gets('devAddress', []);

            if ($devAddresses && !Environment::getInstance()->isProduction()) {
                foreach ($devAddresses as $redirectTo) {
                    $recipients[$redirectTo] = '-> ' . $checkAddress . ' - ' . $checkName;
                }

                continue;
            }

            $recipients[$address] = $name;
        }

        return $recipients;
    }

    /**
     * @param string $instanceKey
     * @param int $ttl
     * @param array $params
     * @return MessageTransport|Container
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return MessageTransport::$defaultKey;
    }

    abstract public function send(Message $message);

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getReplyToAddress()
    {
        return $this->replyToAddress;
    }

    /**
     * @return string
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }
}