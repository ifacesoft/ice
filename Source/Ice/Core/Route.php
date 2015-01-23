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
use Ice\Helper\Arrays;
use Ice\Helper\File;
use Ice\View\Render\Replace;

/**
 * Class Route
 *
 * Core route class
 *
 * example:
 * ```php
 *      $route = [
 *          'route' => '/blog/cabinet/{$0}/post/{$1}',
 *          'actions' => [
 *              'main' => 'Blog:Cabinet_Post_Index',
 *              'title' => ['Ice:Title' => ['title' => 'Cabinet - Post']]
 *          ],
 *          'layout' => 'Blog:Layout_Cabinet',
 *          'params' => [
 *              'blogTranslit' => '([^/]+)',
 *              'postTranslit' => ['([^/]+)', true]
 *           ],
 *          'weight' => 1000
 *      ];
 * ```
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
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

    private static $_defaults = [
        'params' => [],
        'roles' => [],
        'weight' => 0,
    ];

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
        $route = Route::getInstance($routeName);

        if (!$route) {
            self::getLogger()->warning(['Route name "{$0}" not found', $routeName], __FILE__, __LINE__);
            return '/';
        }

        return Replace::getInstance()->fetch($route->getRoute(), $params, View_Render::TEMPLATE_TYPE_STRING);
    }

    /**
     * Create new instance of route
     *
     * @param string $name
     * @param null $hash
     * @return Route
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function create($name, $hash = null)
    {
        $routes = self::getRoutes();

        if (!isset($routes[$name])) {
            $name = 'ice_404';
        }

        return new Route($name, Arrays::defaults(self::$_defaults, $routes[$name]));
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

                $route = Arrays::defaults(self::$_defaults, $route);
                $route['route'] = $context . $route['route'];

                if (isset($routes[$routeName])) {
                    self::getLogger()->info(['Route name "{$0}" already defined in other route config', $routeName], null, true);
                    continue;
                }

                if (substr_count($route['route'], '{$') != count($route['params'])) {
                    self::getLogger()->info(['Count of params in {$0} not equal with count of defined params', $route['route']], null, true);
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
}