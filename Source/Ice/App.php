<?php

namespace Ice;

use Composer\Installer\PackageEvent;
use Composer\Script\Event;
use Ice\Action\Http_Status;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Route;
use Ice\Data\Provider\Cli;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Router;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;

class App
{
    private static $response = null;

    private static $context = null;

    public static function run()
    {
        Logger::fb('bootstrapping finished - ' . Profiler::getReport(BOOTSTRAP_CLASS), 'application', 'LOG');

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        try {
            /**
             * @var Action $actionClass
             * @var array $input
             */
           list($actionClass, $input) = App::getAction();

            App::getResponse()->setView($actionClass::call($input));
        } catch (Redirect $e) {
            App::getResponse()->setRedirectUrl($e->getRedirectUrl());
        } catch (Http_Bad_Request $e) {
            $actionClass = Http_Status::getClass();
            App::getResponse()->setView($actionClass::call(['code' => 400, 'exception' => $e]));
        } catch (Http_Not_Found $e) {
            $actionClass = Http_Status::getClass();
            App::getResponse()->setView($actionClass::call(['code' => 404, 'exception' => $e]));
        } catch (\Exception $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application failure', __FILE__, __LINE__, $e);
            } else {
                $actionClass = Http_Status::getClass();
                App::getResponse()->setView($actionClass::call(['code' => 500, 'exception' => $e]));
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
     * Return http response
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getResponse()
    {
        if (App::$response) {
            return App::$response;
        }

        return App::$response = Response::create();
    }

    /**
     * Return application context
     *
     * @return Action_Context
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getContext()
    {
        if (App::$context) {
            return App::$context;
        }

        return App::$context = Action_Context::create();
    }

    private static function getAction()
    {
        if (Request::isCli()) {
            ini_set('memory_limit', '1024M');

            $input = Cli::getInstance()->get();
            $actionClass = $input['actionClass'];
            unset($input['actionClass']);
        } elseif (Request::isAjax()) {
            $input = Data_Provider_Request::getInstance()->get();
            $actionClass = $input['call'];
            unset($input['actionClass']);
        } else {
            $router = Router::getInstance();
            $route = Route::getInstance($router->get('routeName'));
            $method = $route->gets('request/' . $router->get('method'));

            list($actionClass, $input) = each($method);
            $actionClass = Action::getClass($actionClass);
        }

        return [$actionClass, $input];
    }

    public static function install(Event $event)
    {
        $composer = $event->getComposer();
        var_dump($composer);
        echo 'Ice installation complete';
    }

    public static function update(Event $event)
    {
        $composer = $event->getComposer();
        var_dump($composer);
        echo 'Ice installation complete';
    }
}
