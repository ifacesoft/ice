<?php
/**
 * Ice data provider implementation router class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Config;
use Ice\Core\Data_Provider;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Request as Core_Request;
use Ice\Core\Route;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;

/**
 * Class Router
 *
 * Data provider for router data
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 */
class Router extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Router/default';

    protected $_options = null;

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return Core_Request::getMethod() . Core_Request::uri(true);
    }

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultDataProviderKey()
    {
        return Router::DEFAULT_DATA_PROVIDER_KEY;
    }

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($key = null)
    {
        return $key ? $this->getConnection()[$key] : $this->getConnection();
    }

    /**
     * Get instance connection of data provider
     * @throws Exception
     * @return Config[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @throws \Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function set($key, $value = null, $ttl = null)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws \Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function delete($key, $force = true)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function incr($key, $step = 1)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function decr($key, $step = 1)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function flushAll()
    {
        throw new \Exception('Implement flushAll() method.');
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return bool
     * @throws Http_Forbidden
     * @throws Http_Not_Found
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected function connect(&$connection)
    {
        $dataProvider = Route::getDataProvider('route');

        $key = $this->getKey();

        /** @var Route $route */
        $route = $dataProvider->get($key);

        if ($route) {
            $connection = $route;
            return true;
        }

        $method = strstr($key, '/', true);

        if (empty($method)) {
            $method = 'GET';
        }

        $url = strstr($key, '/');

        $matchedRoutes = [];
        $foundRoutes = [];

        foreach (Route::getRoutes() as $routeName => $route) {
//            Debuger::dump($routeName . ': ' . $url . ' || ' . $route['pattern'] . ' || ' . (int) preg_match($route['pattern'], $url));

            if (!preg_match($route['pattern'], $url)) {
                continue;
            }

            $matchedRoutes[] = $routeName;

            if (empty($route['request'][$method])) {
                continue;
            }

            $weight = is_array($route['weight']) ? reset($route['weight']) : $route['weight'];

            if (!isset($foundRoutes[$weight])) {
                $foundRoutes[$weight] = [
                    $routeName,
                    $route['pattern'],
                    $route['params']
                ];
            }
        }

        if (empty($foundRoutes)) {
            if (!empty($matchedRoutes)) {
                $this->getLogger()->warning(['Route not found for {$0} request of {$1}, but matched routes for pattern {$2}', [$method, $url, $route['pattern']]], __FILE__, __LINE__);
            }

            throw new Http_Not_Found(['route for url \'{$0}\' not found', $url]);
        }

        krsort($foundRoutes, SORT_NUMERIC);

        list($routeName, $pattern, $params) = reset($foundRoutes);

        $baseMatches = [];
        preg_match_all($pattern, $url, $baseMatches, PREG_SET_ORDER);

        $data = [
            'routeName' => $routeName,
            'method' => $method
        ];

        if (count($baseMatches[0]) - 1 < count($params)) {
            $baseMatches[0][] = '';
        }

        $data = array_merge($data, array_combine(array_keys($params), array_slice($baseMatches[0], 1)));

        return (bool)$connection = $dataProvider->set($key, $data);
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }
}