<?php

namespace Ice;

use Ice\Action\Front_Ajax;
use Ice\Action\Http_Status;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Route;
use Ice\Core\View;
use Ice\Data\Provider\Cli;
use Ice\Data\Provider\Router;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Object;

class App
{
    private static $_response = null;

    private static $_context = null;

    public static function run()
    {
        Logger::fb('bootstrapping finished - ' . Profiler::getReport(BOOTSTRAP_CLASS), 'application', 'LOG');

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

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
                $actionClass = Action::getClass($actionClass);
                $view = $actionClass::call($input);
            }

            App::getResponse()->setContent($view);
        } catch (Redirect $e) {
            App::getResponse()->setRedirectUrl($e->getRedirectUrl());
        } catch (Http_Bad_Request $e) {
            $actionClass = Http_Status::getClass();
            $view = $actionClass::call(['code' => 400, 'exception' => $e]);
            App::getResponse()->setContent($view);
        } catch (Http_Not_Found $e) {
            $actionClass = Http_Status::getClass();
            $view = $actionClass::call(['code' => 404, 'exception' => $e]);
            App::getResponse()->setContent($view);
        } catch (\Exception $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application failure', __FILE__, __LINE__, $e);
            } else {
                $actionClass = Http_Status::getClass();
                $view = $actionClass::call(['code' => 500, 'exception' => $e]);
                App::getResponse()->setContent($view);
            }
        }

        App::getResponse()->send();

        Profiler::setPoint(__CLASS__, $startTime, $startMemory);
        Logger::fb('running finished - ' . Profiler::getReport(__CLASS__), 'ice application', 'LOG');

        Logger::renderLog();

        if (!Request::isCli() && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
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
        if (App::$_context) {
            return App::$_context;
        }

        return App::$_context = Action_Context::create();
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
        if (App::$_response) {
            return App::$_response;
        }

        return App::$_response = Response::create();
    }
}