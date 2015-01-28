<?php

namespace Ice\Core;

use Ice\Model\User;

class Security
{
    public static function checkAccess($roles, $permission)
    {
        if (empty($roles)) {
            return true;
        }

        return in_array($permission, array_merge(array_intersect_key($roles, array_flip(Security::getRoleNames()))));
    }

    public static function getRoleNames()
    {
        if (isset($_SESSION['roleNames'])) {
            return $_SESSION['roleNames'];
        }

        return $_SESSION['roleNames'] = Security::getUser() ? ['Ice:User'] : ['Ice:Guest'];
    }

    public static function getUser()
    {
        if (isset($_SESSION['userPk'])) {
            return User::getModel($_SESSION['userPk'], '*');
        }

        return null;
    }
}