<?php

namespace Ice\Security;

use Ice\Core\Security;

class Symfony extends Security
{
    public function check(array $access)
    {
        $denied = false;

        if (isset($access['roles'])) {
            $denied = true;

            global $kernel;
            $securityAuthorizationChecker = $kernel->getContainer()->get('security.authorization_checker');

            foreach ((array) $access['roles'] as $role) {
                if (true === $securityAuthorizationChecker->isGranted($role)) {
                    $denied = false;
                }
            }
        }

        return !$denied;
    }
}