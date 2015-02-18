<?php
/**
 * Ice core route class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Router;
use Ice\Exception\Http_Not_Found;
use Ice\Helper\Config as Helper_Config;
use Ice\Helper\File;
use Ice\View\Render\Replace;

/**
 * Class Route
 *
 * Core route class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Route extends Container
{
    use Core;

    /**
     * Route name
     *
     * @var string
     */
    private $_routeName = null;
    /**
     * Route data
     *
     * @var array
     */
    private $_route = null;

    /**
     * Private constructor of route
     *
     * @param $routeName
     * @param $route
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($routeName, $route)
    {
        $this->_routeName = $routeName;
        $this->_route = $route;
    }

    /**
     * Create new instance of route
     *
     * @param string $name
     * @return Route
     * @throws Http_Not_Found
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function create($name)
    {
        $routes = self::getRoutes();

        if (!isset($routes[$name])) {
            throw new Http_Not_Found(['Creating route by name \'{$0}\' failed', $name]);
        }

        return new Route($name, $routes[$name]);
    }

    /**
     * Return all routes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getRoutes()
    {
        $dataProvider = Route::getDataProvider('routes');

        if ($routes = $dataProvider->get('routes')) {
            return $routes;
        }

        $routeFilePathes = [];

        foreach (Module::get() as $moduleConfig) {
            $routeFilePathes[$moduleConfig['context']] = $moduleConfig['path'] . 'Config/Ice/Core/Route.php';
        }

        return $dataProvider->set('routes', self::getRouteFileData($routeFilePathes));
    }

    /**
     * Return route data from file
     *
     * @param array $routeFilePathes
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    private static function getRouteFileData(array $routeFilePathes)
    {
        $routes = [];

        $defaultConfig = Config::getDefault(__CLASS__);

        foreach ($routeFilePathes as $context => $routeFilePath) {
            $configFromFile = File::loadData($routeFilePath);

            if (!is_array($configFromFile)) {
                self::getLogger()->warning(['Не валидный файл конфиг: {$0}', $routeFilePath], __FILE__, __LINE__);
                continue;
            }

            foreach ($configFromFile as $routeName => $route) {
                if ($routeName[0] == '_') {
                    $configFilePathes = [];

                    $class = 'Ice\Core\\' . substr(strstr($routeFilePath, '.php', true), strrpos($routeFilePath, '/') + 1);

                    foreach (Loader::getFilePath($class . '' . $routeName, '.php', 'Config/', true, false, false, true) as $configFilePath) {
                        $configFilePathes[$context . $route] = $configFilePath;
                    }

                    $routes += self::getRouteFileData($configFilePathes);
                    continue;
                }

                $route = array_merge_recursive($route, $defaultConfig);
                $route['route'] = $context . $route['route'];

                if (isset($routes[$routeName])) {
                    Route::getLogger()->warning(['Route name "{$0}" already defined in other route config', $routeName], __FILE__, __LINE__);
                    continue;
                }

                if (substr_count($route['route'], '{$') != count($route['params'])) {
                    Route::getLogger()->warning(['Count of params in {$0} not equal with count of defined params', $route['route']], __FILE__, __LINE__, null, [$route['route'], $route['params']]);
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

                $route['pattern'] = Replace::getInstance()->fetch('#^' . $route['route'] . '$#', $patterns, View_Render::TEMPLATE_TYPE_STRING);

                foreach ($route['request'] as &$request) {
                    list($actionClass, $actionParams) = each($request);

                    if(is_int($actionClass)) {
                        $actionClass = $actionParams;
                        $actionParams = [];
                    }

                    $request = [Action::getClass($actionClass) => $actionParams];
                }

                $routes[$routeName] = $route;
            }
        }

        return $routes;
    }

    /**
     * Return default route key by uri
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
        return Router::getInstance()->get('routeName');
    }

    /**
     * Return self route name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRouteName()
    {
        return $this->_routeName;
    }

    public function getLayoutActionClassName($method)
    {
        return $this->get('request/' . $method . '/layout');
    }

    /**
     * Get config param value
     *
     * @param string|null $key
     * @param bool $isRequired
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated 0.4 todo: use directly array
     * @version 0.4
     * @since 0.4
     */
    private function get($key = null, $isRequired = true)
    {
        $params = $this->gets($key, $isRequired);

        return empty($params) ? null : reset($params);
    }

    /**
     * Get config param values
     *
     * @param string|null $key
     * @param bool $isRequired
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function gets($key = null, $isRequired = true)
    {
        return Helper_Config::gets($this->_route, $key, $isRequired);
    }

    public function getResponseRedirect($method)
    {
        return Route::getUrl($this->gets('request/' . $method . '/response/redirect', false));
    }

    /**
     * Generate url by route
     *
     * @param $routeName
     * @param array $params
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getUrl($routeName, array $params = [])
    {
        if (!$routeName) {
            return null;
        }

        if (is_array($routeName)) {
            list($routeName, $params) = each($routeName);
        }

        $route = Route::getInstance($routeName);

        if (!$route) {
            return $routeName;
        }

        return Replace::getInstance()->fetch($route->getRoute(), $params, View_Render::TEMPLATE_TYPE_STRING);
    }

    /**
     * Return instance of Route
     *
     * @param null $key
     * @param null $ttl
     * @return Route
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function getInstance($key = null, $ttl = null)
    {
        if (!$key) {
            $key = Route::getDefaultKey();
        }

        return parent::getInstance($key, $ttl);
    }

    /**
     * Return route string
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRoute()
    {
        return $this->getData()['route'];
    }

    /**
     * return route data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getData()
    {
        return $this->_route;
    }

    /**
     * Return actions includes in layout
     *
     * @param $method
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getActionClassNames($method)
    {
        return $this->gets('request/' . $method . '/actions');
    }
}