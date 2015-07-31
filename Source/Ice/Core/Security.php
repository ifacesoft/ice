<?php

namespace Ice\Core;

abstract class Security extends Container
{
    private static $defaultClassKey = null;

    protected function autologin() {
        return null;
    }

    /**
     * @return Security_User
     */
    abstract public function getUser();

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();

    /**
     * @param null $key
     * @param null $ttl
     * @return Security
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $class = self::getClass();

        return new $class($key);
    }

    public function init()
    {
        if (Security::$defaultClassKey === null) {
            Security::$defaultClassKey = get_class($this);
            return;
        }

        Security::getLogger()->warning('Security already initialized', __FILE__, __LINE__);
    }

    protected static function getDefaultClassKey()
    {
        return Security::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Check access by roles
     *
     * @param array $access
     * @return bool
     */
    abstract public function check(array $access);

//    public static function checkAccess($roles, $permission)
//    {
//        if (empty($roles)) {
//            return true;
//        }
//
//        return in_array($permission, array_merge(array_intersect_key($roles, array_flip(Security::getRoleNames()))));
//    }

//    public static function getRoleNames()
//    {
//        if (isset($_SESSION['roleNames'])) {
//            return $_SESSION['roleNames'];
//        }
//
//        return $_SESSION['roleNames'] = Security::getUser() ? ['Ice:User'] : ['Ice:Guest'];
//    }
//
//    public static function getUser()
//    {
//        if (isset($_SESSION['userPk'])) {
//            return User::getModel($_SESSION['userPk'], '*');
//        }
//
//        return null;
//    }
}
