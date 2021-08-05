<?php

namespace Ice\Exception;

use Ice\App;

class Security_User_NotActive extends Security
{
    public function __construct($message, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = 0)
    {
        parent::__construct($message, $errcontext, $previous, $errfile, $errline, $errno);

        App::getResponse()->setStatusCode(401);
    }
}
