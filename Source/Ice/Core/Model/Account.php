<?php

namespace Ice\Core;

use Ice\Core\Model;
use Ice\Core\Model\Security_User;
use Ice\Model\Token;
use Ice\Model\User;
use Ice\Widget\Account_Form;
use Ice\Widget\Account_Form_Register;

abstract class Model_Account extends Model
{
    /**
     * Check is expired account
     *
     * @return bool
     */
    abstract public function isExpired();

    /**
     * @return User|Model
     */
    public function getUser()
    {
        /** @var Model $userModelClass */
        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');
        return $this->fetchOne($userModelClass, '*', true);
    }

    /**
     * @param array $values
     * @return array
     */
    abstract public function securityVerify(array $values);

    abstract public function signIn(Account_Form $accountForm);

    abstract public function signUp(Account_Form_Register $accountForm);

    abstract public function sendRegisterConfirm(Account_Form $accountForm, Token $token);

    abstract public function prolongate($expired);

    public function signUpUser(array $userData)
    {
        $security = Security::getInstance();

        if ($security->isAuth()) {
            $user = $security->getUser()->set($userData);
        } else {
            /** @var Security_User|Model $userModelClass */
            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

            $user = $userModelClass::create($userData);
        }

        return $user->save();
    }
}