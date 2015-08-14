<?php

namespace Ice\Security;

use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Security;
use Ice\Core\Security_Account;
use Ice\Core\Security_User;
use Ice\Data\Provider\Session;
use Ice\Data\Provider\Security as Data_Provider_Security;

class Ice extends Security
{
    const SECURITY_USER = 'user';
    const SESSION_USER_KEY = 'userKey';
    const SESSION_ACCOUNT_KEY = 'accountKey';

    /**
     * Check access by roles
     *
     * @param array $access
     * @return bool
     */
    public function check(array $access)
    {
        return false;
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    public function isAuth()
    {
        return Session::getInstance()->get(Ice::SESSION_ACCOUNT_KEY);
    }

    /**
     * @return Security_User
     */
    public function getUser()
    {
        return Data_Provider_Security::getInstance()->get(Ice::SECURITY_USER);
    }

    /**
     * @param Security_Account|Model $account
     * @return bool
     */
    public function login(Security_Account $account)
    {
        try {
            $user = $account->getUser();

            Session::getInstance()->set(Ice::SESSION_USER_KEY, $user->getPkValue());
            Session::getInstance()->set(Ice::SESSION_ACCOUNT_KEY, $account->getPkValue());

            Data_Provider_Security::getInstance()->set(Ice::SECURITY_USER, $user);
        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return Ice::getLogger()->exception('Ice security login failed', __FILE__, __LINE__, $e);
        }

        return true;
    }

    public function logout()
    {
        Data_Provider_Security::getInstance()->delete(Ice::SECURITY_USER);

        Session::getInstance()->flushAll();

        $this->autologin();
    }

    protected function autologin()
    {
        $userKey = Session::getInstance()->get(Ice::SESSION_USER_KEY);

        if (!$userKey) {
            $userKey = 1;
        }

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        Data_Provider_Security::getInstance()->set(Ice::SECURITY_USER, $userModelClass::getModel($userKey, '*'));
        Session::getInstance()->set(Ice::SESSION_USER_KEY, $userKey);
    }

    /**
     * All user roles
     *
     * @return string[]
     */
    public function getRoles()
    {
        return [];
    }
}