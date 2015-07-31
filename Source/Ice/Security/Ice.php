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

    public function init()
    {
        parent::init();

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        $user = $userModelClass::getModel(Session::getInstance()->get('user_pk'), '*');

        if (!$user) {
            $user = $this->autologin();

            if ($user) {
                Session::getInstance()->set('user_pk', $user->getPkValue());
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
        return Session::getInstance()->get('isAuth');
    }

    /**
     * @return Security_User
     */
    public function getUser()
    {
        return Data_Provider_Security::getInstance()->get(Ice::USER);
    }
}