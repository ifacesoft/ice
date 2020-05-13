<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Access_Denied_Security;
use Ice\Model\User;


/**
 * Class Security
 * @package Ice\Core
 *
 * @todo может это все делать через Environtment?
 */
abstract class Security extends Container
{
    use Core;

    public static $loaded = false;

    private $checkRoles = null;

    public static function init() {
        Security::$loaded = false;
        Security::getInstance();
        Security::$loaded = true;
    }

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->checkRoles = Config::getInstance(Security::class)->get('checkRoles', 1);

        $this->autologin();
    }

    /**
     * @return null|string
     */
    public function isCheckRoles()
    {
        return (boolean) $this->checkRoles;
    }

    abstract protected function autologin();

    public static function checkAccess($roles, $message)
    {
        if (!$roles || Security::getInstance()->check((array)$roles)) {
            return;
        }

        throw new Access_Denied_Security($message);
    }

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    abstract public function check(array $roles);

    /**
     * @param null $instanceKey
     * @param null $ttl
     * @param array $params
     * @return Security|Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Add user roles
     */
    abstract public function addRoles($roles);

    /**
     * All user roles
     *
     * @return string[]
     */
    abstract public function getRoles();

    /**
     * @return User
     */
    abstract public function getUser();

    /**
     * @return Model_Account
     */
    abstract public function getAccount();

    /**
     * @param Model_Account $account
     * @param null $dataSourceKey
     * @return Model_Account|null
     */
    abstract public function login(Model_Account $account, $dataSourceKey = null);

    /**
     * @return bool
     * @throws Exception
     */
    public function logout() {
        self::getInstance()->removeInstance();
        self::init();

        return true;
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();

    /**
     * @return DataProvider
     * @throws Exception
     */
    protected function getDataProviderSessionAuth()
    {
        return parent::getDataProviderSession('auth');
    }
}
