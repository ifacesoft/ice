<?php

namespace Ice\Core;

interface Security_Account
{
    /**
     * Check is expired account
     *
     * @return bool
     */
    public function isExpired();

    /**
     * @return Security_User|Model
     */
    public function getUser();
}