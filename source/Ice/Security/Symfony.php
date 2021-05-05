<?php

namespace Ice\Security;

use Application\Sonata\UserBundle\Entity\User;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Model_Account;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class Symfony
 *
 * @package Ice\Security
 *
 * @deprecated
 *
 */
class Symfony extends Ice
{
    private $symfonyUser = null;

    /**
     * Symfony constructor.
     * @param array $data
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     */
    protected function __construct(array $data)
    {
        if (session_status() === PHP_SESSION_NONE) {
            Environment::getInstance();

            if ($kernel = $this->getKernel()) {
                if ($container = $this->getKernel()->getContainer()) {
                    $container->get('session')->start();
                }
            }
        }

        parent::__construct($data);
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

    public static function isSymfony()
    {
        return class_exists(\App\Kernel::class, false);
    }

    /**
     * @param Model_Account $account
     * @param array $data
     * @return Model_Account|null
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    public function login(Model_Account $account, array $data)
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
}