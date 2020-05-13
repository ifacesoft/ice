<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Date;
use SessionHandlerInterface;

abstract class SessionHandler extends Container implements SessionHandlerInterface
{
    use Core;

    private $sessionPk = null;

    private $sessionCreatedAt = null;

    private $sessionDataHash = 'd41d8cd98f00b204e9800998ecf8427e'; // md5('');

    /**
     * @param $sessionData
     * @return string
     */
    public function isSessionDataUpdated($sessionData)
    {
        return md5($sessionData) != $this->sessionDataHash;
    }

    /**
     * @param string $sessionData
     * @return string
     */
    public function setSessionDataHash($sessionData)
    {
        $this->sessionDataHash = md5($sessionData);

        return $sessionData;
    }

    /**
     * @return null
     */
    public function getSessionPk()
    {
        return $this->sessionPk;
    }

    /**
     * @param null $sessionPk
     */
    public function setSessionPk($sessionPk)
    {
        $this->sessionPk = $sessionPk;
    }

    /**
     * @return null
     */
    public function getSessionCreatedAt()
    {
        return $this->sessionCreatedAt;
    }

    /**
     * @param null $sessionCreatedAt
     */
    public function setSessionCreatedAt($sessionCreatedAt)
    {
        $this->sessionCreatedAt = $sessionCreatedAt;
    }

//    protected function getPk()
//    {
//        return $this->session['session_pk'];
//    }
//
//    protected function setPk($session_id) {
//        $this->session['session_pk'] = $session_id;
//    }
//
//    protected function getData()
//    {
//        return $this->session['session_data'];
//    }
//
//    protected function getReadCount()
//    {
//        return $this->session['session_read_count'];
//    }

    /**
     * @param array $session
     */
    protected function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @param string $instanceKey
     * @param null $ttl
     * @param array $params
     * @return SessionHandler|Container
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

    protected function getCurrentTime()
    {
        return Date::get(null, null, false);
    }
}