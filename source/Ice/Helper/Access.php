<?php

namespace Ice\Helper;

use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Request;
use Ice\Core\Security;
use Ice\Exception\Access_Denied_Environment;
use Ice\Exception\Access_Denied_Request;
use Ice\Exception\Access_Denied_Security;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;

class Access
{
    /**
     * @param array $access
     * @throws Exception
     * @throws Access_Denied_Environment
     * @throws Access_Denied_Request
     * @throws Access_Denied_Security
     * @throws Config_Error
     * @throws FileNotFound
     */
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
