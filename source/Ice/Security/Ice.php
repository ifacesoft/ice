<?php

namespace Ice\Security;

use Ice\Core\DataProvider;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Model_Account;
use Ice\Core\Security;
use Ice\Core\Session;
use Ice\DataProvider\Session as DataProvider_Session;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;
use Ice\Model\Account_Password_Phone;
use Ice\Model\User;

class Ice extends Security
{
    const SESSION_USER_CLASS = 'userClass';
    const SESSION_USER_KEY = 'userKey';
    const SESSION_ACCOUNT_CLASS = 'accountClass';
    const SESSION_ACCOUNT_KEY = 'accountKey';

    /**
     * @var User
     */
    private $user;

    /**
     * @var Model_Account
     */
    private $account;

    private $roles = [];

    private $firstVisit = false;

    /**
     * Ice constructor.
     * @param array $data
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     */
    protected function __construct(array $data)
    {
        Environment::getInstance();
        Session::init();

        if (!isset($_SESSION[Security::class]['roles'])) {
            $_SESSION[Security::class]['roles'] = [];
        }

        $this->roles = (array)$_SESSION[Security::class]['roles'];

        parent::__construct($data);
    }

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     * @throws Exception
     */
    public function check(array $roles)
    {
        $userRoles = $this->getRoles();

        return array_intersect($roles, $userRoles) || \in_array('ROLE_ICE_SUPER_ADMIN', $userRoles);
    }

    /**
     * All user roles
     *
     * @return string[]
     * @throws Exception
     */
    public function getRoles()
    {
        return array_merge($this->isAuth() ? ['ROLE_ICE_USER'] : ['ROLE_ICE_GUEST'], $this->roles);
    }

    /**
     * Check logged in
     *
     * @return bool
     * @throws Exception
     */
    public function isAuth()
    {
        // todo: Авторизация должна зависеть не от наличия ид аккаунта, а от его типа
        return (bool)$this->getAccountKey();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getAccountKey()
    {
        return $this->getDataProviderSessionAuth()->get(Ice::SESSION_ACCOUNT_KEY, null);
    }

    /**
     * @return DataProvider
     * @throws Exception
     */
    protected function getDataProviderSessionAuth()
    {
        return DataProvider_Session::getInstance(get_class($this), 'auth');
    }

    public function addRoles($roles)
    {
        $_SESSION[Security::class]['roles'] = array_unique(array_merge($_SESSION[Security::class]['roles'], (array)$roles));
        $this->roles = array_unique(array_merge($this->roles, $_SESSION[Security::class]['roles']));
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Model_Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @throws Exception
     */
    protected function autologin()
    {
        $this->firstVisit = !$this->getDataProviderSessionAuth()->get();

        /** @var User $user */
        $user = User::getModel($this->getUserKey(), '*', null, -1);

        /** @var Model_Account|string $accountModelClass */
        $accountModelClass = $this->getAccountModelClass();

        if ($accountKey = $this->getAccountKey()) {
            /** @var Model_Account $account */
            $account = $accountModelClass::getModel($accountKey, '*', null, -1);
        } else {
            $account = $accountModelClass::create(['user__fk' => $user->getPkValue()]);
        }

        if ($this->isFirstVisit()) {
            $this->login($account, null);
        } else {
            $this->setUser($user);
            $this->setAccount($account);
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getUserKey()
    {
        return $this->getDataProviderSessionAuth()->get(Ice::SESSION_USER_KEY, 1);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getAccountModelClass()
    {
        return $this->getDataProviderSessionAuth()->get(Ice::SESSION_ACCOUNT_CLASS, Account_Password_Phone::class);
    }

    /**
     * @return bool
     */
    public function isFirstVisit()
    {
        return $this->firstVisit;
    }

    /**
     * @param Model_Account $account
     * @param array $data
     * @return bool|Model_Account
     * @throws Exception
     * @throws FileNotFound
     * @throws \Ice\Exception\Error
     */
    public function login(Model_Account $account, array $data)
    {
        try {
            $dataProviderSessionAuth = $this->getDataProviderSessionAuth();

            $user = $account->getUser();

            $dataProviderSessionAuth->set([Ice::SESSION_USER_CLASS => get_class($user)]);
            $dataProviderSessionAuth->set([Ice::SESSION_USER_KEY => $user->getPkValue()]);
            $dataProviderSessionAuth->set([Ice::SESSION_ACCOUNT_CLASS => get_class($account)]);
            $dataProviderSessionAuth->set([Ice::SESSION_ACCOUNT_KEY => $account->getPkValue()]);

            $this->setUser($user);
            $this->setAccount($account);

            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id();
            }
        } catch (\Exception $e) {
            $this->getLogger()->error('Ice security login failed', __FILE__, __LINE__, $e);

            $this->logout();

            return null;
        }

        return $account;
    }

    /**
     * @param User $user
     */
    private function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Model_Account $account
     */
    private function setAccount(Model_Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function logout()
    {
        $this->user = null;
        $this->account = null;

        $this->getDataProviderSessionAuth()->flushAll();
        $_SESSION = []; // todo: после логаута и flushall глобальная $_SESSION[] не очищается, а должна.

        return parent::logout();
    }
}
