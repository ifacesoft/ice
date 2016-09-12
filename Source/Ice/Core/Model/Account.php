<?php

namespace Ice\Core;

use Ice\Core\Model;
use Ice\Model\Token;
use Ice\Model\User;
use Ice\Widget\Account_Form;

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

    abstract public function signUp(Account_Form $accountForm);

    abstract public function sendRegisterConfirm(Account_Form $accountForm, Token $token);

    abstract public function prolongate($expired);
}