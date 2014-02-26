<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.01.14
 * Time: 22:22
 */

namespace ice\data\provider;

use ice\core\Data_Provider;
use ice\Exception;
use Locale;

class Request extends Data_Provider
{

    public static function getDefaultKey()
    {
        return 'Request:http/';
    }

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
        throw new Exception('Implement set() method.');
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
    protected function connect(&$connection)
    {
        if (!isset($_SERVER)) {
            return false;
        }

        $connection = (array)$_REQUEST;

        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER ['HTTP_HOST'] = 'default';
            $_SERVER ['SERVER_NAME'] = 'default';
        }

        $connection['agent'] = isset($_SERVER['HTTP_USER_AGENT'])
            ? $_SERVER['HTTP_USER_AGENT']
            : $_SERVER['SHELL'];

        $connection['uri'] = isset($_SERVER['REQUEST_URI'])
            ? $_SERVER['REQUEST_URI']
            : 'php://input';

        $connection['locale'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ? Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            : 'en_US';

        if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $connection['ip'] = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var(
                $_SERVER['HTTP_X_FORWARDED_FOR'],
                FILTER_VALIDATE_IP
            )
        ) {
            $connection['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
//            $connection['ip'] = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
            $connection['ip'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $connection['ip'] = '0.0.0.0';
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

    /**
     * @param $connection
     * @return boolean
     */
    protected function switchScheme(&$connection)
    {
        return true;
    }
}