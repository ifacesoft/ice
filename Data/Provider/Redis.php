<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.01.14
 * Time: 15:43
 */

namespace ice\data\provider;


use ice\core\Data_Provider;

class Redis extends Data_Provider
{

    /**
     * @param $connection
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        // TODO: Implement switchScheme() method.
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        // TODO: Implement connect() method.
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        // TODO: Implement close() method.
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