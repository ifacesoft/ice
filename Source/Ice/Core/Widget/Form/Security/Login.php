<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Login extends Widget_Form_Security
{
    /**
     * Verify account by form values
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    protected abstract function verify(Security_Account $account, $values);
}