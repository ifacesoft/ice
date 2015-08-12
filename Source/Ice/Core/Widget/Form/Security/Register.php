<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Register extends Widget_Form_Security
{
    /**
     * Sing up by account
     *
     * @param Security_Account $account
     * @return Security_Account|Model
     */
    protected function singUp(Security_Account $account)
    {
        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        return $account->set(['user' => $userModelClass::create()->save()])->save();
    }

    /**
     * Register by input form data
     *
     * @return Security_Account|Model
     */
    public abstract function register();
}