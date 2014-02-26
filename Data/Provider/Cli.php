<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 13.02.14
 * Time: 14:23
 */

namespace ice\data\provider;

use ice\core\Data_Provider;
use ice\Exception;

class Cli extends Data_Provider
{

    public function get($key = null)
    {
        $connection = $this->getConnection();

        if (!$connection) {
            return null;
        }

        return $key ? $connection[$key] : $connection;
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
     * @throws Exception
     * @return boolean
     */
    protected function connect(&$connection)
    {
        if (!isset($_SERVER ['argv']) || !isset($_SERVER ['argc'])) {
            throw new Exception('This script is for console use only.');
        }

        if (empty ($_SERVER ['argv']) || count($_SERVER ['argv']) < 2) {
            throw new Exception('Invalid command line. Usage: cli.php Action_Call param=value');
        }

        $connection = array();

        $connection['action'] = next($_SERVER ['argv']);

        while ($arg = next($_SERVER ['argv'])) {
            list($param, $value) = explode('=', $arg);
            if (!$value) {
                throw new Exception('Invalid command line. Usage: cli.php Action_Call param=value');
            }
            $connection[$param] = $value;
        }

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
}