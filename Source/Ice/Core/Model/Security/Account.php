<?php

namespace Ice\Core\Model;

use Ice\Core\Model;
use Ice\Model\User;


interface Security_Account
{
    /**
     * Check is expired account
     *
     * @return bool
     */
    public function isExpired();

    /**
     * @return User|Model
     */
    public function getUser();

    public function securityVerify(array $values);

    public function securityHash(array $values);
}