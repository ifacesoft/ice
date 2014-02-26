<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.01.14
 * Time: 14:27
 */

namespace ice\data\provider;

use ice\core\Data_Provider;

class Mysqli extends Data_Provider
{
    /**
     * @param $connection \Mysqli
     * @return bool
     */
    protected function connect(&$connection)
    {
        $connection = mysqli_init();
        $isConnected = $connection->real_connect(
            $this->getOption('host'),
            $this->getOption('username'),
            $this->getOption('password'),
            null,
            $this->getOption('port')
        );

        $connection->set_charset($this->getOption('charset'));

        return $isConnected;
    }

    /**
     * @param $connection \Mysqli
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        return $connection->select_db($this->getScheme());
    }

    /**
     * @return \Mysqli
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * @param $connection \Mysqli
     * @return bool
     */
    protected function close(&$connection)
    {
        return $connection->close();
    }

    public function get($key = null)
    {
        // TODO: Implement get() method.
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