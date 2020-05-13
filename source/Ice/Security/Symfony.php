<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Model_Account;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Auth;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Symfony extends Ice
{
    private $symfonyUser = null;

//    /**
//     * Check access by roles
//     *
//     * @param array $roles
//     * @return bool
//     */
//    public function check(array $roles)
//    {
//        if (!$roles || !$this->isCheckRoles()) {
//            return true;
//        }
//
//        if (Symfony::isSymfony()) {
//            /** @var AuthorizationChecker $securityAuthorizationChecker */
//            $securityAuthorizationChecker = $this->getKernel()->getContainer()->get('security.authorization_checker');
//
//            foreach ($roles as $role) {
//                try {
//                    if (true === $securityAuthorizationChecker->isGranted($role)) {
//                        return true;
//                    }
//                } catch (\Exception $e) {
//                    return false;
//                }
//            }
//        }
//
//        return parent::check($roles);
//    }

    public static function isSymfony()
    {
        return class_exists(\App\Kernel::class, false);
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
     * @throws Exception
     */
    public function isAuth()
    {
        if (!Symfony::isSymfony()) {
            return parent::isAuth();
        }

        $securityContext = $this->getKernel()->getContainer()->get('security.authorization_checker');

        return parent::isAuth() && ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'));
    }

    /**
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    protected function autologin()
    {
        parent::autologin();

        if (!Symfony::isSymfony()) {
            return;
        }

        try {
            $this->reloadToken();
        } catch (\Exception $e) {
            $this->getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);
        }
    }

    /**
     * @param Model_Account $account
     * @param null $dataSourceKey
     * @return Model_Account|null
     * @throws Exception
     */
    public function login(Model_Account $account, $dataSourceKey = null)
    {
        $account = parent::login($account);

        if (!$account) {
            return null;
        }

        if (Symfony::isSymfony()) {
            try {
                $this->reloadToken();
            } catch (\Exception $e) {
                $this->getLogger()->exception('Symfony security login failed', __FILE__, __LINE__, $e);

                $this->logout();

                return null;
            }
        }

        return $account;
    }

    /**
     * @param string $firewall
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws Exception
     */
    public function reloadToken($firewall = 'default')
    {
        /** @var User $symfonyUser */
        $symfonyUser = null;

        if ($entityManager = $this->getEntityManager()) {
            $symfonyUser = $entityManager->find(User::class, $this->getUser()->getPkValue());
        }

        if (!$symfonyUser) {
            $this->getLogger()->exception('Symfony user not found', __FILE__, __LINE__);
        }

        /** @var ContainerInterface $container */
        $container = $this->getKernel()->getContainer();

        $token = new UsernamePasswordToken($symfonyUser, null, $firewall, $symfonyUser->getRoles());
        $container->get('security.token_storage')->setToken($token);

        $session = $container->get('session');
        $session->set('_security_' . $firewall, serialize($token));

        $this->setSymfonyUser($symfonyUser);
    }

    /**
     * @return EntityManager
     *
     */
    private function getEntityManager()
    {
        if ($kernel = $this->getKernel()) {
            return $kernel->getContainer()->get('doctrine')->getManager();
        }

        return null;
    }

    public function logout()
    {
        if (Symfony::isSymfony()) {
            $this->setSymfonyUser(null);

            if ($kernel = $this->getKernel()) {
                $kernel->getContainer()->get('security.token_storage')->setToken(null);
            }
        }

        return parent::logout();
    }

    /**
     * All user roles
     *
     * @return string[]
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws Exception
     */
    public function getRoles()
    {
        $symfonyRoles = Symfony::isSymfony()
            ? $this->getSymfonyUser()->getRoles()
            : unserialize($this->getUser()->get('roles', serialize([])));

        return array_merge(parent::getRoles(), $symfonyRoles);
    }

    /**
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function getSymfonyUser()
    {
        $symfonyUser = null;

        if (!self::isSymfony()) {
            return $symfonyUser;
        }

        /** @var User $symfonyUser */
        if ($symfonyUser = $this->symfonyUser) {
            return $symfonyUser;
        }

        if ($token = $this->getKernel()->getContainer()->get('security.token_storage')->getToken()) {
            $symfonyUser = $token->getUser();
        }

        if (!\is_object($symfonyUser)) {
            $symfonyUser = $this->getEntityManager()->find(User::class, $this->getUser()->getPkValue());
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