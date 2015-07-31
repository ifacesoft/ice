<?php

namespace Ice\Security;

use Ice\Core\Debuger;
use Ice\Data\Provider\Security as Data_Provider_Security;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpKernel\Kernel;

class Symfony extends Ice
{
    const SYMFONY_USER = 'symfonyUser';

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        global $kernel;
        return $kernel;
    }

    /**
     * Check access by roles
     *
     * @param array $access
     * @return bool
     */
    public function check(array $access)
    {
        $denied = false;

        if (isset($access['roles'])) {
            $denied = true;

            $securityAuthorizationChecker = $this->getKernel()->getContainer()->get('security.authorization_checker');

            foreach ((array)$access['roles'] as $role) {
                if (true === $securityAuthorizationChecker->isGranted($role)) {
                    $denied = false;
                }
            }
        }

        return !$denied || parent::check($access);
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    public function isAuth()
    {
        $securityContext = $this->getKernel()->getContainer()->get('security.context');

        return (
            $securityContext->isGranted('IS_AUTHENTICATED_FULLY') ||
            $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) && parent::isAuth();
    }

    public function getSymfonyUser()
    {
        return $securityContext = $this->getKernel()->getContainer()->get('security.context')->getToken()->getUser();
    }
}