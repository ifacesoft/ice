<?php

namespace Ice\Helper;

use Ice\Core\Environment;
use Ice\Core\Request;
use Ice\Core\Security;

class Access
{
    public static function check(array $access)
    {
        $message = isset($access['message']) ? $access['message'] : 'Forbidden';

        Environment::checkAccess($access['env'], $message);
        Request::checkAccess($access['request'], $message);
        Security::checkAccess($access['roles'], $message);
    }
}
