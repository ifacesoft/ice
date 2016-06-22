<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Core\Model\Security_Account;
use Ice\Core\Model\Security_User;
use Ice\Exception\Access_Denied_Security;

abstract class Security extends Container
{
    use Core;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->autologin();
    }

    public static function checkAccess($roles, $message)
    {
        if (!$roles || Security::getInstance()->check((array)$roles)) {
            return;
        }

        throw new Access_Denied_Security($message);
    }

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    abstract public function check(array $roles);

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Security
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

    /**
     * All user roles
     *
     * @return string[]
     */
    abstract public function getRoles();

    /**
     * @return Security_User
     */
    abstract public function getUser();

    /**
     * @param $account
     * @return bool
     */
    abstract public function login(Security_Account $account);

    abstract public function logout();

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();

    abstract protected function autologin();
}
