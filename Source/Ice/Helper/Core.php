<?php

namespace Ice\Helper;

use Ice\Core\MessageTransport;
use Ice\Core\Router;
use Ice\Core\Security;
use Ice\Core\SessionHandler;

class Core
{

}

/**
 * @return SessionHandler
 */
function sessionHandler()
{
    return SessionHandler::getInstance();
}

/**
 * @return Security
 */
function security()
{
    return Security::getInstance();
}

/**
 * @return Router
 */
function router()
{
    return Router::getInstance();
}

/**
 * @return MessageTransport
 */
function messageTransport()
{
    return MessageTransport::getInstance();
}