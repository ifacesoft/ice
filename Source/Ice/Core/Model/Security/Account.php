<?php

namespace Ice\Core\Model;

use Ice\Core\Model;

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
}