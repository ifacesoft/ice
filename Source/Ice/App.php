<?php

namespace Ice;

use Composer\Config;
use Composer\Script\Event;
use Ice\Action\Install;
use Ice\Action\Layout_Main;
use Ice\Action\Upgrade;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Route;
use Ice\Data\Provider\Cli;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Router;
use Ice\Exception\Access_Denied;
use Ice\Exception\Error;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\File;

class App
{
    private static $response = null;

    private static $context = null;

    public static function run()
    {
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
            $httpStatusAction = [['Ice:Http_Status' => 'main', ['code' => 400, 'exception' => $e]]];
            App::getResponse()->setView(Layout_Main::call(['actions' => $httpStatusAction]));
        } catch (Access_Denied $e) {
            $httpStatusAction = [['Ice:Http_Status' => 'main', ['code' => 403, 'exception' => $e]]];
            App::getResponse()->setView(Layout_Main::call(['actions' => $httpStatusAction]));
        } catch (Http_Not_Found $e) {
            $httpStatusAction = [['Ice:Http_Status' => 'main', ['code' => 404, 'exception' => $e]]];
            App::getResponse()->setView(Layout_Main::call(['actions' => $httpStatusAction]));
        } catch (\Exception $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application failure', __FILE__, __LINE__, $e);
            } else {
                $httpStatusAction = [['Ice:Http_Status' => 'main', ['code' => 500, 'exception' => $e]]];
                $view = Layout_Main::call(['actions' => $httpStatusAction]);
                $view->setError($e->getMessage());

                App::getResponse()->setView($view);
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

            $_SERVER['REQUEST_URI'] = implode(' ', $_SERVER['argv']);

            $input = Cli::getInstance()->get();
            $actionClass = $input['actionClass'];
            unset($input['actionClass']);

            return [Action::getClass($actionClass), $input];
        }

        if (Request::isAjax()) {
            $input = Data_Provider_Request::getInstance()->get();

            if (!empty($input['actionClass'])) {
                $actionClass = $input['actionClass'];
                unset($input['actionClass']);

                return [Action::getClass($actionClass), $input];
            }

            throw new \Exception('Undefined action for ajax request');
        }

        $router = Router::getInstance();
        $route = Route::getInstance($router->get('routeName'));
        if ($route && $routeRequest = $route->gets('request/' . $router->get('method'), false)) {
            list($actionClass, $input) = each($routeRequest);

            return [Action::getClass($actionClass), $input];
        }

        throw new Http_Not_Found([
            'Route not found for {$0} request of {$1}, but matched for other method',
            [$router->get('method'), $router->get('url')]
        ]);
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
//        $composer = $event->getComposer();
//
//        /** @var Config $composerConfig */
//        $composerConfig = $composer->getConfig();

        define('ICE_DIR', dirname(dirname(__DIR__)) . '/');
        define('VENDOR_DIR', dirname(dirname(ICE_DIR)) . '/');
        define('MODULE_DIR', getcwd() . '/');

        $moduleConfigFilePath = MODULE_DIR . 'Config/Ice/Core/Module.php';
        $configFilePath = MODULE_DIR . 'Config/Ice/Core/Config.php';
        $environmentConfigFilePath = MODULE_DIR . 'Config/Ice/Core/Environment.php';

        if (
            file_exists($moduleConfigFilePath) &&
            file_exists($configFilePath) &&
            file_exists($environmentConfigFilePath)
        ) {
            require_once ICE_DIR . 'bootstrap.php';

            echo Upgrade::call()->getContent();

            return;
        }

        File::createData($moduleConfigFilePath, Module::$defaultConfig);

        require_once ICE_DIR . 'bootstrap.php';

        echo Install::call()->getContent();
    }
}