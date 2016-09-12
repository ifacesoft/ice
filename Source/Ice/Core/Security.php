<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Core\Model\Security_User;
use Ice\Exception\Access_Denied_Security;
use Ice\Model\Account;
use Ice\Model\User;

abstract class Security extends Container
{
    use Core;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->autologin();
    }

    abstract protected function autologin();

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
     * @param null $instanceKey
     * @param null $ttl
     * @param array $params
     * @return Security|Container
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

    /**
     * All user roles
     *
     * @return string[]
     */
    abstract public function getRoles();

    /**
     * @return Security_User|User
     */
    abstract public function getUser();

    /**
     * @param $account
     * @return Model_Account
     */
    abstract public function login(Model_Account $account);

    abstract public function logout();

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();
}
