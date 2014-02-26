<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 22.01.14
 * Time: 1:26
 */

namespace ice\data\provider;

use ice\core\Data_Provider;

class Session extends Data_Provider
{

    /**
     * @param $connection
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        return true;
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        if (!isset($_SESSION)) {
            session_start();
            $_SESSION['PHPSESSID'] = session_id();
            $connection = $_SESSION;
        }

        return isset($connection);
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        unset($_SESSION);
        return true;
    }

    public function get($key = null)
    {
        return $key ? $this->getConnection()[$key] : $this->getConnection();
    }

    public function set($key, $value, $ttl = 3600)
    {
        // TODO: Implement set() method.
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    public function inc($key, $step = 1)
    {
        // TODO: Implement inc() method.
    }

    public function dec($key, $step = 1)
    {
        // TODO: Implement dec() method.
    }

    public function flushAll()
    {
        // TODO: Implement flushAll() method.
    }
}