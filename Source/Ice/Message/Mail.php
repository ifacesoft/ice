<?php

namespace Ice\Message;

use Ice\Core\Message;

class Mail extends Message
{
    /**
     * ReplyTo addresses
     *
     * @var array|string|null
     */
    private $replyTo = null;

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
     * @return array|null|string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param array|string $replyTo
     * @return Mail
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @return array|null|string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param array|string $cc
     * @return Mail
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
        return $this;
    }

    /**
     * @return array|null|string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param array|string $bcc
     * @return Mail
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }
}