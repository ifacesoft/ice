<?php
/**
 * Ice data provider implementation router class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\Config;
use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Request as Core_Request;
use Ice\Core\Route;
use Ice\Exception\Error;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Http_Redirect;
use Ice\Exception\RouteNotFound;

/**
 * Class Router
 *
 * Data provider for router data
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Router extends DataProvider
{
    private static $cacheData = [];

    /**
     * @param string $key
     * @param string $index
     * @return Router|DataProvider
     * @throws Exception
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
        return Core_Request::method() . Core_Request::uri(true);
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     * @throws Exception
     */
    public function get($key = null, $default = null, $require = false)
    {
        $connection = null;

        try {
            $connection = $this->getConnection();

            if (empty($key)) {
                return $connection;
            }

            $value = array_key_exists($key, $connection) ? $connection[$key] : $default;
        } catch (Http_Redirect $e) {
            throw $e;
        } catch (\Exception $e) {
//            $value = null;
//            Logger::getInstance(__CLASS__)->warning(['Param {$0} not found', $key], __FILE__, __LINE__, $e, $connection);
            throw $e;
        }

        if ($require && ($value === null || $value === '')) {
            $dataProviderClass = get_class($this);

            throw new Error(['Param {$0} from data provider {$1} is require', [$key, $dataProviderClass]]);
        }

        return $value;
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
     * @param array $values
     * @param  null $ttl
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        // TODO: Implement getKeys() method.
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
     * @throws RouteNotFound
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

        // todo: $key must be METHOD/url format
        if ($url === false) {
            $url = '/';
        }

        $route = $this->getRoute($url, $method);

        $baseMatches = [];
        preg_match_all($route['pattern'], $url, $baseMatches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

        if (count($baseMatches[0]) - 1 < count($route['params'])) {
            $baseMatches[0][] = null;
        }

        $route['routeParams'] += array_combine(array_keys($route['params']), array_slice($baseMatches[0], 1));

        $route += $route['routeParams'];

        return (bool)$connection = $dataProvider->set([$key => $route])[$key];
    }

    public function getRoute($url, $method)
    {
        list($matchedRoutes, $foundRoutes) = $this->getRoutes($url, $method);

        if (empty($foundRoutes)) {
            if (empty($matchedRoutes)) {
                throw new RouteNotFound(['Route for {$0} {$1} not found', [$method, $url]]);
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
     * @throws Error
     * @throws Exception
     * @throws Http_Redirect
     * @throws \Ice\Exception\Config_Error
     * @throws \Ice\Exception\RouteNotFound
     */
    private function getRoutes($url, $method)
    {
        if (empty($url) || empty($method)) {
            throw new Error('Method and Uri is required');
        }

        $cacheTag = $method . ':' . $url;

        if (isset(Router::$cacheData[$cacheTag])) {
            return Router::$cacheData[$cacheTag];
        }

        $matchedRoutes = [];
        $foundRoutes = [];

        $isDebug = Environment::getInstance()->isDevelopment() && Core_Request::getParam('routing');

        if ($isDebug) {
            Debuger::dump($cacheTag);
        }

        /**
         * @var string $routeName
         * @var Route $route
         */
        foreach (Route::getRoutes() as $routeName => $route) {
            if ($isDebug) {
                Debuger::dump($routeName . ': ' . $route->get('pattern') . ' || ' . (int)preg_match($route->get('pattern'), $url));
            }

            $redirect = $route->get('redirect', false);

            // Так не делаю, потому, что приходится запрашивать урл по роуту (например GET... а у нас находится только по POST... такая фигня)
            // if (!preg_match($route->get('pattern'), $url) || (!$route->get('request/' . $method, null) && !$redirect)) {
            if (!preg_match($route->get('pattern'), $url)) {
                continue;
            }

            $weight = $route->get('weight');

            if (!isset($matchedRoutes[$weight])) {
                $matchedRoutes[$weight] = [
                    'routeName' => $routeName,
                    'pattern' => $route->get('pattern'),
                    'params' => $route->gets('params'),
                    'url' => $url,
                    'method' => $method,
                    'redirect' => $redirect,
                    'routeParams' => (array)$route->gets('request/' . $method, [])
                ];
            }

            if (empty($matchedRoutes[$weight]['routeParams'])) {
                continue;
            }

            if (!isset($foundRoutes[$weight])) {
                $foundRoutes[$weight] = $matchedRoutes[$weight];
            }
        }

        if ($isDebug) {
            die();
        }

        if (!empty($matchedRoutes)) {
            $route = !empty($foundRoutes)
                ? reset($foundRoutes)
                : reset($matchedRoutes);

            if ($route['redirect']) {
                $routeName = $route['redirect'][0] == '_'
                    ? $route['routeName'] . $route['redirect']
                    : $route['redirect'];

                throw new Http_Redirect(Route::getInstance($routeName)->getUrl());
            }
        }

        return Router::$cacheData[$cacheTag] = array($matchedRoutes, $foundRoutes);
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

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function expire($key, $ttl)
    {
        // TODO: Implement expire() method.
    }

    /**
     * Check for errors
     *
     * @return void
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    function checkErrors()
    {
        // TODO: Implement checkErrors() method.
    }
}
