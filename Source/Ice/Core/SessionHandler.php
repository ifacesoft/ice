<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 03.02.16
 * Time: 11:14
 */

namespace Ice\Core;

use Ice\Core;
use SessionHandlerInterface;

abstract class SessionHandler extends Container implements SessionHandlerInterface
{
    use Core;

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    protected function init(array $data)
    {
        // TODO: Implement init() method.
    }

    /**
     * @param string $key
     * @param null $ttl
     * @param array $params
     * @return SessionHandler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }
}