<?php

namespace Ice\Core;

interface Security_User
{
    public function isExpired();

    public function isActive();
}