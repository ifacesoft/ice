<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 31.12.13
 * Time: 14:55
 */

namespace ice\data\provider;

use ice\core\Data_Provider;
use ice\core\Model;
use ice\Exception;

class Defined extends Data_Provider
{
    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        /** @var Model $modelName */
        $modelName = $this->getScheme();
        $connection = $modelName::getDefinedConfig()->getParams();
        return (bool)count($connection);
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
}