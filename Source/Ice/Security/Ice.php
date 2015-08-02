<?php

namespace Ice\Security;

use Doctrine\Common\Util\Debug;
use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Security;
use Ice\Core\Security_User;
use Ice\Data\Provider\Session;
use Ice\Data\Provider\Security as Data_Provider_Security;

class Ice extends Security
{
    const USER = 'user';
    const SESSION_USER_KEY = 'user_pk';
    const SESSION_AUTH_FLAG = 'isAuth';

    public function init()
    {
        parent::init();

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        $user = $userModelClass::getModel(Session::getInstance()->get(Ice::SESSION_USER_KEY), '*');

        if (!$user) {
            $user = $this->autologin();

            if ($user) {
                Session::getInstance()->set(Ice::SESSION_USER_KEY, $user->getPkValue());
            }
        }

        Data_Provider_Security::getInstance()->set(Ice::USER, $user);
    }


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
        return Session::getInstance()->get(Ice::SESSION_AUTH_FLAG);
    }

    /**
     * @return Security_User
     */
    public function getUser()
    {
        return Data_Provider_Security::getInstance()->get(Ice::USER);
    }

    /**
     * @param $userKey
     * @return bool
     * @throws \Exception
     */
    public function login($userKey)
    {
        try {
            Session::getInstance()->set(Ice::SESSION_USER_KEY, $userKey);
            Session::getInstance()->set(Ice::SESSION_AUTH_FLAG, 1);

            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

            Data_Provider_Security::getInstance()->set(
                Ice::USER, $userModelClass::getModel(Session::getInstance()->get(Ice::SESSION_USER_KEY), '*')
            );
        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return Ice::getLogger()->exception('Ice security login failed', __FILE__, __LINE__, $e);
        }

        return true;
    }

    public function logout()
    {
        Data_Provider_Security::getInstance()->delete(Ice::USER);

        Session::getInstance()->flushAll();
    }
}