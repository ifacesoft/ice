<?php

namespace Ice\Core;

use Ice\Model\Role;
use Ice\Model\User;

class Security
{

    public static function getUser()
    {
        if (isset($_SESSION['userPk'])) {
            return User::getModel($_SESSION['userPk'], '*');
        }

        return null;
    }

    public static function getRoles()
    {
        if (isset($_SESSION['roleKeys'])) {
            return Role::getCollection($_SESSION['roleKeys']);
        }

        return Role::getEmptyCollection();
    }
}