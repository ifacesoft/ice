<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Ice\Core\Debuger;
use Ice\Data\Provider\Security as Data_Provider_Security;
use Ice\Data\Provider\Session;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
        $securityContext = $this->getKernel()->getContainer()->get('security.authorization_checker');

        return parent::isAuth() && ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') ||
            $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'));
    }

    public function getSymfonyUser()
    {
        return $securityContext = $this->getKernel()->getContainer()->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @param $userKey
     * @return bool
     */
    public function login($userKey)
    {
        parent::login($userKey);

        try {
            $this->auth();
        } catch (\Exception $e) {
            $this->logout();
            $this->autologin();

            return Symfony::getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);
        }

        return true;
    }

    private function auth()
    {
        /** @var EntityManager $doctrine */
        $entityManager = $this->getKernel()->getContainer()->get('doctrine')->getEntityManager();

        /** @var User $user */
        $user = $entityManager->find(
            User::class,
            Session::getInstance()->get(Ice::SESSION_USER_KEY)
        );

        $firewall = 'main';
        $token = new UsernamePasswordToken($user->getUsernameCanonical(), null, $firewall, $user->getRoles());
        $this->getKernel()->getContainer()->get('security.token_storage')->setToken($token);
//            $this->getKernel()->getContainer()->get('security.authentication.manager')->authenticate($token);
//            $session = $this->getKernel()->getContainer()->get('session');
//            $session->set('_security_' . $firewall, serialize($token));
//            $session->save();

        Data_Provider_Security::getInstance()->set(Symfony::SYMFONY_USER,  $user);
    }

    public function logout()
    {
        Data_Provider_Security::getInstance()->delete(Symfony::SYMFONY_USER);

        $this->getKernel()->getContainer()->get('security.token_storage')->setToken(null);

        parent::logout();
    }
}