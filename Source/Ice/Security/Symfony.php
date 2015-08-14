<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Data\Provider\Security as Data_Provider_Security;
use Ice\Data\Provider\Session;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
        $securityContext = $this->getKernel()->getContainer()->get('security.authorization_checker');

        return parent::isAuth() && ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') ||
            $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'));
    }

    /**
     * @return User
     */
    public function getSymfonyUser()
    {
        if ($symfonyUser = Data_Provider_Security::getInstance()->get(Symfony::SECURITY_USER_SYMFONY)) {
            return $symfonyUser;
        }

        $symfonyUser = $this->getKernel()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if (!is_object($symfonyUser)) {
            $symfonyUser = $this->getEntityManager()->find(
                User::class,
                Session::getInstance()->get(Ice::SESSION_USER_KEY)
            );
        }

        return Data_Provider_Security::getInstance()->set(Symfony::SECURITY_USER_SYMFONY, $symfonyUser);
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
                Symfony::getLogger()->exception('Symfony user not found', __FILE__, __LINE__);
            }

            $firewall = 'main';
            $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
            $this->getKernel()->getContainer()->get('security.token_storage')->setToken($token);
//            $this->getKernel()->getContainer()->get('security.authentication.manager')->authenticate($token);
//            $session = $this->getKernel()->getContainer()->get('session');
//            $session->set('_security_' . $firewall, serialize($token));
//            $session->save();

            Data_Provider_Security::getInstance()->set(Symfony::SECURITY_USER_SYMFONY, $user);
        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return Symfony::getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);
        }

        return $account;
    }

    public function logout()
    {
        Data_Provider_Security::getInstance()->delete(Symfony::SECURITY_USER_SYMFONY);

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
        return $this->getKernel()->getContainer()->get('doctrine')->getEntityManager();
    }
}