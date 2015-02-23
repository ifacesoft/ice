<?php
/**
 * Ice application class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

use Ice\Action\Front_Ajax;
use Ice\Action\Http_Status;
use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Container;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Route;
use Ice\Core\View;
use Ice\Data\Provider\Cli;
use Ice\Data\Provider\Router;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Memory;

/**
 * Class Ice
 *
 * Run and flush ice application
 * @author dp <denis.a.shestakov@gmail.com>
 */
class Ice
{
    use Core;

    /**
     * Environment
     *
     * @var Environment
     */
    private static $_environment = null;

    private static $_response = null;

    private static $_context = null;

    /**
     * Ice application start time
     *
     * @access private
     * @var float
     */
    private $_startTime;
    /**
     * Main module name
     *
     * @access private
     * @var string
     */
    private $_moduleName = null;

    /**
     * Private constructor
     *
     * @param $moduleName string main module name
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($moduleName)
    {
        $this->_startTime = microtime(true);
        $this->_moduleName = $moduleName;
    }

    /**
     * Return instance of ice container class
     *
     * @param $class Container
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function get($class)
    {
        return $class::getInstance();
    }

    /**
     * Create new instance of Ice application
     *
     * @param $moduleName string main module name
     * @return Ice
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function create($moduleName)
    {
        return new Ice($moduleName);
    }

    /**
     * Return application context
     *
     * @return Action_Context
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getContext()
    {
        if (Ice::$_context) {
            return Ice::$_context;
        }

        return Ice::$_context = Action_Context::create();
    }

    /**
     * Return application environment
     *
     * @return Environment
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getEnvironment()
    {
        if (Ice::isEnvironment()) {
            return Ice::$_environment;
        }

        $environmentName = null;

        $host = Request::host();

        foreach (Environment::getConfig(null, true, -1)->gets('environments') as $hostPattern => $name) {
            $matches = [];
            preg_match($hostPattern, $host, $matches);

            if (!empty($matches)) {
                $environmentName = $name;
                break;
            }
        }

        return Ice::$_environment = $environmentName
            ? Environment::create($environmentName)
            : Environment::create();
    }

    public static function isEnvironment()
    {
        return Ice::$_environment;
    }

    /**
     * Run executing actions
     *
     * Hierarchical call of actions
     *
     * @return Ice
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function run()
    {
        try {
            /** @var Action $actionClass */
            $actionClass = null;

            /** @var View $view */
            $view = null;

            if (Request::isCli()) {
                ini_set('memory_limit', '1024M');

                $input = Cli::getInstance()->get();
                $actionClass = $input['actionClass'];
                unset($input['actionClass']);

                $view = $actionClass::call($input);
            } elseif (Request::isAjax()) {
                $actionClass = Front_Ajax::getClass();
                $view = $actionClass::call();
            } else {
                $router = Router::getInstance();
                $route = Route::getInstance($router->get('routeName'));
                $method = $route->gets('request/' . $router->get('method'));

                list($actionClass, $input) = each($method);

                $view = $actionClass::call($input);
            }

            Ice::getResponse()->setContent($view);
        } catch (Redirect $e) {
            Ice::getResponse()->setRedirectUrl($e->getRedirectUrl());
        } catch (Http_Bad_Request $e) {
            $actionClass = Http_Status::getClass();
            $view = $actionClass::call(['code' => 400, 'exception' => $e]);
            Ice::getResponse()->setContent($view);
        } catch (Http_Not_Found $e) {
            $actionClass = Http_Status::getClass();
            $view = $actionClass::call(['code' => 404, 'exception' => $e]);
            Ice::getResponse()->setContent($view);
        } catch (\Exception $e) {
            $actionClass = Http_Status::getClass();
            $view = $actionClass::call(['code' => 500, 'exception' => $e]);
            Ice::getResponse()->setContent($view);
        }

        Ice::getResponse()->send();

        if (!Environment::isProduction()) {
            Logger::renderLog();
            Logger::fb('application time: ' . Logger::getUsefulWork(true) . ' | ' .
                'idle time: ' . Logger::microtimeResult($this->_startTime + Logger::getUsefulWork()) . ' | ' .
                Memory::memoryGetUsagePeak(), 'INFO');
        }

        if (!Request::isCli() && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Return http response
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getResponse()
    {
        if (Ice::$_response) {
            return Ice::$_response;
        }

        return Ice::$_response = Response::create();
    }
}