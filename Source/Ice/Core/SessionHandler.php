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

    protected $session = null;

    /**
     * @param string $instanceKey
     * @param null $ttl
     * @param array $params
     * @return SessionHandler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    abstract function getConstFields();

    abstract function getVarFields();
}