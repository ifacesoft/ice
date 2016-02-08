<?php

namespace Ice\Security;

use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Security;
use Ice\Core\Security_Account;
use Ice\Core\Security_User;
use Ice\Data\Provider\Security as Data_Provider_Security;
use Ice\Data\Provider\Session;

class Ice extends Security
{
    const SECURITY_USER = 'user';
    const SESSION_USER_KEY = 'userKey';
    const SESSION_ACCOUNT_KEY = 'accountKey';

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    public function check(array $roles)
    {
        return array_intersect($roles, $this->getRoles()) || in_array('ROLE_ICE_SUPER_ADMIN', $this->getRoles());
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    public function isAuth()
    {
        return (bool) Session::getInstance()->get(Ice::SESSION_ACCOUNT_KEY);
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

//            session_regenerate_id();

//            if (ini_get("session.use_cookies")) {
//                $params = session_get_cookie_params();
//                session_regenerate_id();
//
//                setcookie(session_name(), session_id(), time() + $params["lifetime"],
//                    $params["path"], $params["domain"],
//                    $params["secure"], $params["httponly"]
//                );
//            }

            Session::getInstance()->set(Ice::SESSION_USER_KEY, $user->getPkValue());
            Session::getInstance()->set(Ice::SESSION_ACCOUNT_KEY, $account->getPkValue());

            Data_Provider_Security::getInstance()->set(Ice::SECURITY_USER, $user);



        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return $this->getLogger()->exception('Ice security login failed', __FILE__, __LINE__, $e);
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

        /** @var Model $userModelClass */
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
        return $this->isAuth() ? ['ROLE_ICE_USER'] : ['ROLE_ICE_GUEST'];
    }
}