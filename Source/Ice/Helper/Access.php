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

        if (isset($access['env'])) {
            Environment::checkAccess($access['env'], $message);
        }

        if (isset($access['request'])) {
            Request::checkAccess($access['request'], $message);
        }

        if (isset($access['roles'])) {
            Security::checkAccess($access['roles'], $message);
        }
    }
}
