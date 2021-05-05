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

        $this->login($this->getAccount(), $data);
    }

    /**
     * @param Model_Account $account
     * @param array $data
     * @return Model_Account|null
     * @throws Security_Account_NotFound
     */
    public function login(Model_Account $account, array $data)
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
     * @param array $roles
     * @return bool
     * @throws Exception
     */
    public function check(array $roles)
    {
        $userRoles = $this->getRoles();

        return array_intersect($roles, $userRoles) || in_array('ROLE_ICE_SUPER_ADMIN', $userRoles, true);
    }

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
     * @throws Exception
     */
    public function getRoles()
    {
        return $this->isGuest() ? ['ROLE_ICE_GUEST'] : ['ROLE_ICE_USER'];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isGuest()
    {
        return $this->getAccount()->get('user__fk') == 1;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isSuperAdmin()
    {
        return in_array('ROLE_ICE_SUPER_ADMIN', $this->getRoles());
    }

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

        if (!empty($_COOKIE)) {
            foreach (array_keys($_COOKIE) as $cookieName) {
                unset($_COOKIE[$cookieName]);
            }
        }

        if (!empty($_SESSION)) {
            foreach (array_keys($_SESSION) as $sessionParam) {
                unset($_SESSION[$sessionParam]);
            }
        }

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
