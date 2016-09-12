<?php

namespace Ice\Model;

use Ice\Core\Model;
use Ice\Model\Token;
use Ice\Model\User;
use Ice\Widget\Account_Form;

abstract class Account extends Model
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
    abstract public function getUser();

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