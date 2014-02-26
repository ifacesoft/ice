<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 21.12.13
 * Time: 2:20
 */

namespace ice\data\provider;

use ice\core\Data_Provider;
use ice\core\helper\Request;
use ice\Exception;
use ice\Ice;
use ice\model\ice\Route;

class Router extends Data_Provider
{
    public static function getDefaultKey()
    {
        return 'Router:default/' . Request::uri();
    }

    public function get($key = null)
    {
        $url = $this->getScheme();

        $cacheDataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam('routerDataProviderKey') . $url);

        /** @var Route $_route */
        $_route = $cacheDataProvider->get($url);

        if (!$_route) {
            foreach ($this->getConnection() as $route) {
                $pattern = '#^' . $route->get('route') . '$#';
                foreach ((array)$route->get('patterns') as $var => $routeData) {
                    $replace = $routeData['pattern'];
                    $var = '{$' . $var . '}';
                    if (!empty($routeData['optional'])) {
                        $replace = '(?:' . $replace . ')?';
                    }
                    $pattern = str_replace($var, $replace, $pattern);
                }

//            fb($pattern . ' ' . preg_match($pattern, $url));

                if (preg_match($pattern, $url)) {
                    $route->setData('pattern', $pattern);
                    $_route = $route;
                    break;
                }
            }
        }

        if (!$_route) {
            $_route = Route::create(array());
        }

        if ($pattern = $_route->getData('pattern')) {
            $baseMatches = array();

            preg_match_all($pattern, $url, $baseMatches);

            $params = array('pattern' => $pattern);

            if (!empty($baseMatches[0][0])) {
                $keys = array_keys((array)$_route->get('patterns'));

                foreach ($baseMatches as $i => $data) {
                    if (!$i) {
                        continue;
                    }
                    if (!empty($data[0])) {
                        $params[$keys[$i - 1]] = $data[0];
                    } else {
                        $part = $_route->get('patterns')[$keys[$i - 1]];
                        if (isset($part['default'])) {
                            $params[$keys[$i - 1]] = $part['default'];
                        }
                    }
                }
            }

            $_route->set('params', $params);

            $cacheDataProvider->set('url', $_route);
        }

        return $key ? $_route->getRow()[$key] : array('route' => $_route->getRow());
    }

    public function set($key, $value, $ttl = 3600)
    {
        throw new Exception('Not implemented!');
    }

    public function delete($key)
    {
        throw new Exception('Not implemented!');
    }

    public function inc($key, $step = 1)
    {
        throw new Exception('Not implemented!');
    }

    public function dec($key, $step = 1)
    {
        throw new Exception('Not implemented!');
    }

    /**
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        $connection = Route::getCollection();
        return (bool)$connection->getCount();
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
     * @return Route[]
     */
    public function getConnection()
    {
        return parent::getConnection();
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