<?php

namespace Ice\Core;

interface Security_Account
{
    public function isExpired();

    public function isActive();
}