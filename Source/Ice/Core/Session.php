<?php
/**
 * Ice core session class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\SessionHandler\Native;

/**
 * Class Session
 *
 * Core session handler class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Session
{
    use Core;

    /**
     * Initialization session handler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function init()
    {
        if (session_status() == PHP_SESSION_NONE) {
            $sessionHandler = SessionHandler::getInstance();

            if (!($sessionHandler instanceof Native)) {
                session_set_save_handler($sessionHandler, true);

                ini_set('session.use_cookies', 1);

                session_register_shutdown();
            }

            session_start();
        }
    }

    public static function id()
    {
        return session_id();
    }
}
