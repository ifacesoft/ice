<?php

namespace Ice\Action;

use Ice\Core\Model;
use Ice\Core\Security_Account;

abstract class Security_Register extends Security
{
    /**
     * Register by input form data
     *
     * @param array $userData User defaults
     * @return Model|Security_Account
     */
    abstract public function register(array $userData = []);
}