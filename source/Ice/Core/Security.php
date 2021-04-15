<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Access_Denied_Security;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Account_NotFound;
use Ice\Model\User;

/**
 * Class Security
 * @package Ice\Core
 *
 * @todo может это все делать через Environment?
 */
abstract class Security extends Container
{
    use Core;

    /**
     * @var bool
     */
    public static $loaded = false;

    /**
     * @var Model_Account
     */
    private $account;

    protected function __construct(array $data)
    {
        parent::__construct($data);

        $this->login($this->getAccount());
    }

    /**
     * @param Model_Account $account
     * @param null $dataSourceKey
     * @return Model_Account|null
     * @throws Security_Account_NotFound
     */
    public function login(Model_Account $account, $dataSourceKey = null)
    {
        if (!$account) {
            throw new Security_Account_NotFound('Account not found');
        }

        return $this->account = $account;
    }

    /**
     * @return Model_Account
     */
    public function getAccount($dataSourceKey = null)
    {
        return $this->account;
    }

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
     * @throws Exception
     * @version 1.1
     * @since   1.1
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @param $roles
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
     * @return bool
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public function logout()
    {
        $this->account = null;

        self::getInstance()->removeInstance();
        self::init();

        return true;
    }

    public static function init()
    {
        self::$loaded = false;
        self::getInstance();
        self::$loaded = true;
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();

    /**
     * @return Config
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    final public function getConfig()
    {
        return Config::getInstance(self::class);
    }

    /**
     * @deprecated not need
     */
    final protected function autologin()
    {
    }
}
