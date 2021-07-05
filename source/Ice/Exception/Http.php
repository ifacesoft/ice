<?php

namespace Ice\Exception;

use Ice\App;
use Ice\Core\Exception;

abstract class Http extends Exception
{
    public function __construct($message, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = 0)
    {
        parent::__construct($message, $errcontext, $previous, $errfile, $errline, $errno);

        $response = App::getResponse();

        if ($response->getStatusCode() === 200) {
            $response->setStatusCode($this->getHttpCode());
        }
    }

    abstract function getHttpCode();

    abstract function getHttpMessage();
}