<?php

namespace Ice\Core\Model;

interface Security_User
{
    /**
     * Check is active user
     *
     * @return bool
     */
    public function isActive();

    public function getTimezone();
}