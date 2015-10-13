<?php
/**
 * Ice data provider implementation router class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Action;
use Ice\Core\Config;
use Ice\Core\Data_Provider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Request as Core_Request;
use Ice\Core\Route;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;

/**
 * Class Router
 *
 * Data provider for router data
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Data_Provider
 */
class Router extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Router/default';

    /**
     * @param string $key
     * @param string $index
     * @return Router
     */
    public static function getInstance($key = null, $index = 'default')
    {
        return parent::getInstance($key, $index);
    }

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @since   0.0
     */
    protected static function getDefaultDataProviderKey()
    {
        return Router::DEFAULT_DATA_PROVIDER_KEY;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return Route|Route[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($key = null)
    {
        if (!$key) {
            return $this->getConnection();
        }

        return isset($this->getConnection()[$key]) ? $this->getConnection()[$key] : null;
    }

    /**
     * Get instance connection of data provider
     *
     * @throws Exception
     * @return Config[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @throws \Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function set($key, $value = null, $ttl = null)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws \Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @since   0.0
     */
    public function flushAll()
    {
        throw new \Exception('Implement flushAll() method.');
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        $dataProvider = Route::getDataProvider('route');
        $key = $this->getKey();

        if ($route = $dataProvider->get($key)) {
            $connection = $route;
            return true;
        }

        $method = strstr($key, '/', true);

        if (empty($method)) {
            $method = 'GET';
        }

        $url = strstr($key, '/');

        $route = $this->getRoute($url, $method);

        $baseMatches = [];
        preg_match_all($route['pattern'], $url, $baseMatches, PREG_SET_ORDER);

        if (count($baseMatches[0]) - 1 < count($route['params'])) {
            $baseMatches[0][] = '';
        }

        $route['routeParams'] += array_combine(array_keys($route['params']), array_slice($baseMatches[0], 1));

        $route += $route['routeParams'];

        return (bool)$connection = $dataProvider->set($key, $route);
    }

    public function getRoute($url, $method)
    {
        list($matchedRoutes, $foundRoutes) = $this->getRoutes($url, $method);

        if (empty($foundRoutes)) {
            if (empty($matchedRoutes)) {
                Logger::getInstance(__CLASS__)
                    ->exception(
                        ['Route for url \'{$0}\' not found', $url],
                        __FILE__,
                        __LINE__,
                        null,
                        null,
                        -1,
                        Http_Not_Found::getClass()
                    );
            }

            krsort($matchedRoutes, SORT_NUMERIC);

            return reset($matchedRoutes);
        }

        krsort($foundRoutes, SORT_NUMERIC);

        return reset($foundRoutes);
    }

    /**
     * @param $url
     * @param $method
     * @return array
     * @throws Redirect
     */
    private function getRoutes($url, $method)
    {
        $matchedRoutes = [];
        $foundRoutes = [];

        /**
         * @var string $routeName
         * @var Route $route
         */
        foreach (Route::getRoutes() as $routeName => $route) {
            if (Environment::getInstance()->isDevelopment() && Core_Request::getParam('routing')) {
                Debuger::dump(
                    $routeName . ': ' . $url . ' || ' .
                    $route->get('pattern') . ' || ' .
                    (int)preg_match($route->get('pattern'), $url)
                );
            }

            if (!preg_match($route->get('pattern'), $url)) {
                continue;
            }

            $redirect = $route->get('redirect', false);

            $weight = $route->get('weight');

            if (!isset($matchedRoutes[$weight])) {
                $matchedRoutes[$weight] = [
                    'routeName' => $routeName,
                    'pattern' => $route->get('pattern'),
                    'params' => $route->gets('params'),
                    'url' => $url,
                    'method' => $method,
                    'redirect' => $redirect,
                    'routeParams' => (array) $route->gets('request/' . $method, false)
                ];
            }

            if (empty($matchedRoutes[$weight]['routeParams'])) {
                continue;
            }

            if (!isset($foundRoutes[$weight])) {
                $foundRoutes[$weight] = $matchedRoutes[$weight];
            }
        }

        if (!empty($matchedRoutes)) {
            $route = !empty($foundRoutes)
                ? reset($foundRoutes)
                : reset($matchedRoutes);

            if ($route['redirect']) {
                $routeName = $route['redirect'][0] == '_'
                    ? $route['routeName'] . $route['redirect']
                    : $route['redirect'];

                throw new Redirect(Route::getInstance($routeName)->getUrl());
            }
        }

        return array($matchedRoutes, $foundRoutes);
    }

    /**
     * Close connection with data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }
}
