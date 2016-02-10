<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\DataProvider\Security as DataProvider_Security;
use Ice\DataProvider\Session;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Symfony extends Ice
{
    const SECURITY_USER_SYMFONY = 'symfonyUser';

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
     * @return User
     */
    public function getSymfonyUser()
    {
        if ($symfonyUser = DataProvider_Security::getInstance()->get(Symfony::SECURITY_USER_SYMFONY)) {
            return $symfonyUser;
        }

        $symfonyUser = $this->getKernel()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if (!is_object($symfonyUser)) {
            $symfonyUser = $this->getEntityManager()->find(
                User::class,
                Session::getInstance()->get(Ice::SESSION_USER_KEY)
            );
        }

        return DataProvider_Security::getInstance()->set(Symfony::SECURITY_USER_SYMFONY, $symfonyUser);
    }

    /**
     * @param Security_Account|Model $account
     * @return bool
     */
    public function login(Security_Account $account)
    {
        parent::login($account);

        try {
            /** @var User $user */
            $user = $this->getEntityManager()->find(
                User::class,
                Session::getInstance()->get(Ice::SESSION_USER_KEY)
            );

            if (!$user) {
                $this->getLogger()->exception('Symfony user not found', __FILE__, __LINE__);
            }

            $firewall = 'main';
            $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
            $this->getKernel()->getContainer()->get('security.token_storage')->setToken($token);
//            $this->getKernel()->getContainer()->get('security.authentication.manager')->authenticate($token);
//            $session = $this->getKernel()->getContainer()->get('session');
//            $session->set('_security_' . $firewall, serialize($token));
//            $session->save();

            DataProvider_Security::getInstance()->set(Symfony::SECURITY_USER_SYMFONY, $user);
        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return $this->getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);
        }

        return $account;
    }

    public function logout()
    {
        DataProvider_Security::getInstance()->delete(Symfony::SECURITY_USER_SYMFONY);

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
     * @return EntityManager
     *
     */
    private function getEntityManager()
    {
        return $this->getKernel()->getContainer()->get('doctrine')->getManager();
    }
}