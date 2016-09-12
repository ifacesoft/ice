<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Ice\Core\Debuger;
use Ice\Model\Account;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Symfony extends Ice
{
    private $symfonyUser = null;

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    public function check(array $roles)
    {
        if (!$roles) {
            return true;
        }

        /** @var AuthorizationChecker $securityAuthorizationChecker */
        $securityAuthorizationChecker = $this->getKernel()->getContainer()->get('security.authorization_checker');

        foreach ($roles as $role) {
            try {
                if (true === $securityAuthorizationChecker->isGranted($role)) {
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return parent::check($roles);
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        global $kernel;
        return $kernel;
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    public function isAuth()
    {
        $securityContext = $this->getKernel()->getContainer()->get('security.authorization_checker');

        return parent::isAuth() && ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') ||
            $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'));
    }

    /**
     * @param Account $account
     * @return Account
     */
    public function login(Account $account)
    {
        $account = parent::login($account);

        try {
            /** @var User $symfonyUser */
            $symfonyUser = $this->getEntityManager()->find(
                User::class,
                $this->getDataProviderSession('auth')->get(Ice::SESSION_USER_KEY)
            );

            if (!$symfonyUser) {
                $this->getLogger()->exception('Symfony user not found', __FILE__, __LINE__);
            }

            $firewall = 'main';
            $token = new UsernamePasswordToken($symfonyUser, null, $firewall, $symfonyUser->getRoles());
            $this->getKernel()->getContainer()->get('security.token_storage')->setToken($token);
//            $this->getKernel()->getContainer()->get('security.authentication.manager')->authenticate($token);
//            $session = $this->getKernel()->getContainer()->get('session');
//            $session->set('_security_' . $firewall, serialize($token));
//            $session->save();

            $this->setSymfonyUser($symfonyUser);
        } catch (\Exception $e) {
            $this->logout();

            return $this->getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);
        }

        return $account;
    }

    /**
     * @return EntityManager
     *
     */
    private function getEntityManager()
    {
        return $this->getKernel()->getContainer()->get('doctrine')->getManager();
    }

    public function logout()
    {
        $this->setSymfonyUser(null);
        $this->getKernel()->getContainer()->get('security.token_storage')->setToken(null);

        parent::logout();
    }

    /**
     * All user roles
     *
     * @return string[]
     */
    public function getRoles()
    {
        return array_merge(parent::getRoles(), $this->getSymfonyUser()->getRoles());
    }

    /**
     * @return User
     */
    public function getSymfonyUser()
    {
        /** @var User $symfonyUser */
        if ($symfonyUser = $this->symfonyUser) {
            return $symfonyUser;
        }

        $symfonyUser = $this->getKernel()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if (!is_object($symfonyUser)) {
            $symfonyUser = $this->getEntityManager()->find(
                User::class,
                $this->getDataProviderSession('auth')->get(Ice::SESSION_USER_KEY)
            );
        }

        $this->setSymfonyUser($symfonyUser);

        return $symfonyUser;
    }

    /**
     * @param User $symfonyUser
     */
    protected function setSymfonyUser($symfonyUser)
    {
        $this->symfonyUser = $symfonyUser;
    }
}