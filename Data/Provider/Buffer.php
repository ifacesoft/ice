<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 11.01.14
 * Time: 1:31
 */

namespace ice\data\provider;


use ArrayObject;
use ice\core\Data_Provider;
use ice\Exception;

class Buffer extends Data_Provider
{
    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        $connection = new ArrayObject();
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

    public function get($key = null)
    {
        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            return null;
        }

        $data = $this->getConnection()->$keyPrefix;

        if ($key === null) {
            return $data;
        }

        return isset($data[$key]) ? $data[$key] : null;
    }

    public function set($key, $value, $ttl = 3600)
    {
        if (is_array($key) && $value === null) {
            foreach ($key as $k => $value) {
                $this->set($key, $value, $ttl);
            }

            return;
        }

        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            $this->getConnection()->$keyPrefix = array();
        }

        $data = & $this->getConnection()->$keyPrefix;
        $data[$key] = $value;
    }

    public function delete($key)
    {
        throw new Exception('Implement delete() method.');
    }

    public function inc($key, $step = 1)
    {
        throw new Exception('Implement inc() method.');
    }

    public function dec($key, $step = 1)
    {
        throw new Exception('Implement dec() method.');
    }

    public function flushAll()
    {
        throw new Exception('Implement flushAll() method.');
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        return true;
    }

    /** @return ArrayObject */
    public function getConnection()
    {
        return parent::getConnection();
    }
}