<?php
/**
 * Ice core route class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Http_Not_Found;
use Ice\Helper\File;
use Ice\Render\Replace;
use Ice\Data\Provider\Router as Data_Provider_Router;

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
    /**
     * @param null $routeName
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @return Route
     * @throws Exception
     */
    public static function getInstance($routeName, $postfix = null, $isRequired = false, $ttl = null)
    {
        $routes = self::getRoutes();

        if (!isset($routes[$routeName])) {
            Logger::getInstance(__CLASS__)->exception(
                ['Route {$0} not found', $routeName],
                __FILE__,
                __LINE__,
                null,
                null,
                -1,
                Http_Not_Found::getClass()
            );
        }

        return $routes[$routeName];
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

    /**
     * Return all routes
     *
     * @return Route[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
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
            $routeFilePathes[$module->get('context')] = $module->get(Module::CONFIG_DIR) . 'Ice/Core/Route.php';
        }

        return $dataProvider->set('routes', self::getRouteFileData($routeFilePathes));
    }

    /**
     * Return route data from file
     *
     * @param  array $routeFilePathes
     * @return array
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
            $configFromFile = File::loadData($routeFilePath);

            if (!is_array($configFromFile)) {
                Logger::getInstance(__CLASS__)->warning(['Не валидный файл конфиг: {$0}', $routeFilePath], __FILE__, __LINE__);
                continue;
            }

            foreach ($configFromFile as $routeName => $route) {
                if ($routeName[0] == '_') {
                    $configFilePathes = [];

                    $configFilePathes[$context . $route] =
                        strstr($routeFilePath, '.php', true) . str_replace('_', '/', $routeName) . '.php';

                    $routes += self::getRouteFileData($configFilePathes);
                    continue;
                }

                $route = array_merge_recursive($route, $defaultConfig);
                $route['route'] = $context . $route['route'];

                if (substr_count($route['route'], '{$') != count($route['params'])) {
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
                    if (is_array($paramPattern)) {
                        list($paramPattern, $optional) = $paramPattern;

                        if ($optional) {
                            $paramPattern = '(?:' . $paramPattern . ')?';
                        }
                    }

                    $patterns[$paramName] = $paramPattern;
                }

                $route['pattern'] = Replace::getInstance()->fetch(
                    '#^' . $route['route'] . '$#',
                    $patterns,
                    null,
                    Render::TEMPLATE_TYPE_STRING
                );

                if (isset($route['alias'])) {
                    foreach ($route['alias'] as $method => $alias) {
                        $route['request'][$method] = $routes[$route['alias'][$method]]->gets('request/' . $method, false);
                    }
                }

                $route = Route::create($routeName, $route);

                if (isset($routes[$routeName])) {
                    $route->set($routes[$routeName]->gets());
                    unset($routes[$routeName]);
                }

                $routes[$routeName] = $route;
            }
        }

        return $routes;
    }

    public function getLayoutActionClassName($method)
    {
        return $this->get('request/' . $method . '/layout');
    }

    public function getResponseRedirect($method)
    {
        return Route::getUrl($this->gets('request/' . $method . '/response/redirect', false));
    }

    /**
     * Generate url by route
     *
     * @param  array $params
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getUrl(array $params = [])
    {
        $params = array_filter($params, function ($param) {
            return !is_array($param);
        });

        return Replace::getInstance()->fetch($this->getRoute(), $params, null, Render::TEMPLATE_TYPE_STRING);
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
     * Return route string
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @return Route|null
     */
    public function getParentRoute()
    {
        if ($parentRouteName = $this->get('parent', false)) {
            return Route::getInstance($parentRouteName);
        }

        return null;
    }
}
