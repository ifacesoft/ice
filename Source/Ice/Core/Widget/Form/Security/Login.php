<?php

namespace Ice\Core;

use Ice\Core\Model\Security_Account;

abstract class Widget_Form_Security_Login extends Widget_Security
{
    /**
     * Verify account by form values
     *
     * @param Security_Account|Model $account
     * @param array $values
     * @return boolean
     */
    public abstract function verify(Security_Account $account, $values);
}