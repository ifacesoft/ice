<?php

namespace Ice\Core;

abstract class View
{
    private $raw = null;

    private $content = null;

    abstract public function __construct();

    /**
     * @return null
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content === null ? $this->raw : $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}