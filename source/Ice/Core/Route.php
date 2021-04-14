<?php
/**
 * Ice core route class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Exception\Config_Error;
use Ice\Exception\RouteNotFound;
use Ice\Helper\File;
use Ice\Render\Replace;

/**
 * Class Route
 *
 * Core route class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Route extends Config
{
    const PARAM_DIDITS = '(\d+)';

    public function getLayoutActionClassName($method)
    {
        return $this->get('request/' . $method . '/layout');
    }

    public function getResponseRedirect($method)
    {
        return Route::getUrl($this->gets('request/' . $method . '/response/redirect', []));
    }

    /**
     * Generate url by route with query string
     *
     * @param array $params
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo из квери стринг убирать параметры, которые присутствуют в роуте
     *
     * @version 1.1
     * @since   0.0
     */
    public function getUrl(array $params = [])
    {
        $params = array_filter($params, function ($param) {
            return $param !== null && $param !== '' && !is_array($param);
        });

        $routeParams = $this->gets('params');

        foreach ($routeParams as $name => $param) {
            $param = array_pad((array)$param, 2, false);

            if (!array_key_exists($name, $params) && $param[1]) {
                $params[$name] = '';
            }

            if (array_key_exists($name, $params)) {
                $routeParams[$name] = $params[$name];
            } else {
                unset($routeParams[$name]);
            }
        }

        $url = Replace::getInstance()->fetch($this->getRoute(), $routeParams, null, Render::TEMPLATE_TYPE_STRING);

        return $params ? $url . '?' . http_build_query(array_diff_key($params, $this->gets('params'))) : $url;
    }

    /**
     * Return route string
     *
     * @return mixed
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @return Route|null
     * @throws Exception
     * @throws RouteNotFound
     */
    public function getParentRoute()
    {
        if ($parentRouteName = $this->get('parent', false)) {
            return Route::getInstance($parentRouteName);
        }

        return null;
    }

    /**
     * @param null $routeName
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @param array $selfConfig
     * @return Route
     * @throws Exception
     * @throws RouteNotFound
     */
    public static function getInstance($routeName, $postfix = null, $isRequired = false, $ttl = null, array $selfConfig = [])
    {
        $routes = self::getRoutes();

        if (is_array($routeName)) {
            $routeName = reset($routeName);
        }

        if (!isset($routes[$routeName])) {
            throw new RouteNotFound(['Route {$0} not found', $routeName]);
        }

        return $routes[$routeName];
    }

    /**
     * Return all routes
     * @return Route[]
     * @throws Exception
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function getRoutes()
    {
        $dataProvider = Route::getDataProvider('routes');

        if ($routes = $dataProvider->get('routes')) {
            return $routes;
        }

        $routeFilePathes = [];

        foreach (Module::getAll() as $module) {
            $routeConfigPath = $module->getPath(Module::CONFIG_DIR) . 'Ice/Core/Route.php';

            if ($context = $module->get('context', '')) {
                $routeFilePathes[$context] = $routeConfigPath;
            } else {
                $routeFilePathes[] = $routeConfigPath;
            }
        }

        return $dataProvider->set(['routes' => self::getRouteFileData($routeFilePathes)])['routes'];
    }
    //
    //    /**
    //     * Return instance of Route
    //     *
    //     * @param null $key
    //     * @param null $ttl
    //     * @return Route
    //     *
    //     * @author dp <denis.a.shestakov@gmail.com>
    //     *
    //     * @version 0.4
    //     * @since 0.4
    //     */
    //    public static function getInstance($key = null, $ttl = null, array $params = [])
    //    {
    //        if (!$key) {
    //            $key = Route::getDefaultKey();
    //        }
    //
    //        return parent::getInstance($key, $ttl);
    //    }

    /**
     * Return route data from file
     *
     * @param array $routeFilePathes
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    private static function getRouteFileData(array $routeFilePathes)
    {
        $routes = [];

        $defaultConfig = Config::getDefault(__CLASS__);

        foreach ($routeFilePathes as $context => $routeFilePath) {
            if (\is_int($context)) {
                $context = '';
            }

            $configFromFile = File::loadData($routeFilePath, false);

            if (!$configFromFile) {
                continue;
            }

            if (!\is_array($configFromFile)) {
                Logger::getInstance(__CLASS__)->warning(['Не валидный файл конфиг: {$0}', $routeFilePath], __FILE__, __LINE__);
                continue;
            }

            foreach ($configFromFile as $routeName => $route) {
                if (strpos($routeName, '_') === 0) {
                    $configFilePathes = [];

                    $configFilePathes[$context . $route] = strstr($routeFilePath, '.php', true) . str_replace('_', '/', $routeName) . '.php';

                    $routes += self::getRouteFileData($configFilePathes);
                    continue;
                }

                $route = array_merge_recursive($route, $defaultConfig->gets());
                $route['route'] = $context . $route['route'];

                if (substr_count($route['route'], '{$') !== \count($route['params'])) {
                    Logger::getInstance(__CLASS__)->warning(
                        ['Count of params in {$0} not equal with count of defined params', $route['route']],
                        __FILE__,
                        __LINE__,
                        null,
                        [$route['route'], $route['params']]
                    );
                    continue;
                }

                $patterns = [];

                foreach ($route['params'] as $paramName => $paramPattern) {
                    list($pattern, $optional) = array_pad((array)$paramPattern, 2, false);

                    if ($optional) {
                        $pattern = '(?:' . $pattern . ')?';
                    }

                    $patterns[$paramName] = $pattern;
                }

                $route['pattern'] = Replace::getInstance()->fetch(
                    '#^' . $route['route'] . '$#',
                    $patterns,
                    null,
                    Render::TEMPLATE_TYPE_STRING
                );

                if (isset($route['alias'])) {
                    foreach ($route['alias'] as $method => $alias) {
                        $route['request'][$method] = $routes[$route['alias'][$method]]->gets('request/' . $method, []);
                    }
                }

                $route = self::create($routeName, $route);

                if (isset($routes[$routeName])) {
                    $route->set($routes[$routeName]->gets());
                    unset($routes[$routeName]);
                }

                $routes[$routeName] = $route;
            }
        }

        return $routes;
    }

    /**
     * @param $configRouteName
     * @param array $configData
     * @return Route
     */
    public static function create($configRouteName, array $configData = [])
    {
        return parent::create($configRouteName, $configData);
    }
}
