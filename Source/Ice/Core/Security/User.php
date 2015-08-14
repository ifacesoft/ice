<?php

namespace Ice\Core;

interface Security_User
{
    /**
     * Check is active user
     *
     * @return bool
     */
    public function isActive();
}