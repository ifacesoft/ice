<?php

namespace Ice\Exception;

use Ice\Core\Exception;

abstract class Http extends Exception
{
    abstract function getHttpCode();

    abstract function getHttpMessage();
}