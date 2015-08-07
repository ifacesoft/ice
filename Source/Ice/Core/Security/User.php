<?php

namespace Ice\Core;

interface Security_User
{
    /**
     * Check is expired user
     *
     * @return bool
     */
    public function isExpired();

    /**
     * Check is active user
     *
     * @return bool
     */
    public function isActive();
}