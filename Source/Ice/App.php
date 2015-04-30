<?php

namespace Ice;

use Composer\Config;
use Composer\Script\Event;
use Ice\Action\Check;
use Ice\Action\Http_Status;
use Ice\Action\Install;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Debuger;
use Ice\Core\Environment;
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
use Ice\Exception\ModuleNotFound;
use Ice\Exception\Redirect;

class App
{
    private static $response = null;

    private static $context = null;

    public static function run()
    {
        Logger::fb(
            Profiler::getReport(BOOTSTRAP_CLASS, '/' . Environment::getInstance()->getName()),
            __CLASS__,
            'LOG'
        );

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $actionClass = 'unknown';

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

        Profiler::setPoint($actionClass, $startTime, $startMemory);

        Logger::fb(Profiler::getReport($actionClass), __CLASS__, 'LOG');

        Logger::renderLog();

        if (!Request::isCli() && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
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
            $routeRequest = Route::getInstance($router->get('routeName'))->gets('request/' . $router->get('method'));
            list($actionClass, $input) = each($routeRequest);
            $actionClass = Action::getClass($actionClass);
        }

        return [$actionClass, $input];
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

    public static function update(Event $event)
    {
        $composer = $event->getComposer();

        /** @var Config $composerConfig */
        $composerConfig = $composer->getConfig();

        define('ICE_DIR', dirname(dirname(__DIR__)) . '/');
        define('MODULE_DIR', getcwd() . '/');

        require_once ICE_DIR . 'bootstrap.php';

        Install::call();
        Check::call();
    }
}
