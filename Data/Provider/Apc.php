<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 21.12.13
 * Time: 1:17
 */

namespace ice\data\provider;

use ice\core\Data_Provider;
use ice\Exception;
use ice\Ice;

class Apc extends Data_Provider
{

    public function get($key = null)
    {
        if ($key === null) {
            throw new Exception("Not implemented get all values from data provider APC");
        }

        return $this->getConnection()->fetch($this->getKey($key));
    }

    public function set($key, $value, $ttl = 3600)
    {
        return $this->getConnection()->store($this->getKey($key), $value, $ttl);
    }

    public function delete($key)
    {
        return $this->getConnection()->delete($this->getKey($key));
    }

    public function inc($key, $step = 1)
    {
        return $this->getConnection()->inc($this->getKey($key), $step);
    }

    public function dec($key, $step = 1)
    {
        return $this->getConnection()->dec($this->getKey($key), $step);
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        if (!class_exists('ice\core\data\provider\adapter\Apc', false)) {
            require_once Ice::getEnginePath() . 'Core/Data/Provider/Adapter/Apc.php';
        }

        $connection = new \ice\core\data\provider\adapter\Apc();

        return true;
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }

    /**
     * @return \ice\core\data\provider\adapter\Apc
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    public function flushAll()
    {
        $this->getConnection()->clearCache();
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        return true;
    }
}