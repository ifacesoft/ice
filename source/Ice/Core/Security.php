<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Access_Denied_Security;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;
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

    protected function __construct(array $data)
    {
        parent::__construct($data);

        $this->autologin();
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
    public function logout()
    {
        self::getInstance()->removeInstance();
        self::init();

        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookieParams = session_get_cookie_params();

            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);

                setcookie(
                    $name,
                    '',
                    time() - 3600,
                    $cookieParams['path'],
                    $cookieParams['domain'],
                    $cookieParams['secure'],
                    $cookieParams['httponly']
                );

//                setcookie($name, '', time()-1000);
//                setcookie($name, '', time()-1000, '/');
            }
        }

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
}
