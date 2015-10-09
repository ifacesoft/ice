<?php

namespace Ice;

use Composer\Config;
use Composer\Script\Event;
use Ice\Action\Install;
use Ice\Action\Layout;
use Ice\Action\Upgrade;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Session;
use Ice\Data\Provider\Cli as Data_Provider_Cli;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Exception\Access_Denied;
use Ice\Exception\Error;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\File;
use Ice\Widget\Http_Status;

class App
{
    private static $response = null;

    private static $context = null;

    public static function run()
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        if (Request::isCli()) {
            $actionClass = Data_Provider_Cli::getInstance()->get('actionClass');
        } else {
            Request::init();
            Session::init();

            if (Request::isAjax()) {
                $actionClass = Data_Provider_Request::getInstance()->get('actionClass');
            } else {
                $actionClass = Layout::getClass();
            }
        }

        try {
            if (!$actionClass) {
                throw new Error('action class not found');
            }

            $result = $actionClass::call();
        } catch (\Exception $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application (Cli): run action failure', __FILE__, __LINE__, $e);

                $result = ['error' => Logger::getInstance(__CLASS__)->info($e->getMessage(),Logger::DANGER)];
            } else {
                try {
                    throw $e;
                } catch (Redirect $e) {
                    $result['redirectUrl'] = $e->getRedirectUrl();
                } catch (Http_Bad_Request $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 400, 'message' => $e->getMessage()])];
                } catch (Access_Denied $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 403, 'message' => $e->getMessage()])];
                } catch (Http_Not_Found $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 404, 'message' => $e->getMessage()])];
                } catch (\Exception $e) {
                    Logger::getInstance(__CLASS__)->error('Application (Http): run action failure', __FILE__, __LINE__, $e);

                    $result = [
                        'content' => Http_Status::getInstance('app', null, ['message' => $e->getMessage()]),
                        'error' => Logger::getInstance(__CLASS__)->info($e->getMessage(), Logger::DANGER)
                    ];
                }
            }
        }

        if (Request::isCli()) {
            if (isset($result['error'])) {
                fwrite(STDERR, $result['error'] . "\n");
            } else {
                try {
                    fwrite(STDOUT, $result['content'] . "\n");
                } catch (\Exception $e) {
                    fwrite(STDERR, Logger::getInstance(__CLASS__)->error('Application (Cli): render content failure', __FILE__, __LINE__, $e) . "\n");
                }
            }
        } else {
            try {
                App::getResponse()->send($result);
            } catch (\Exception $e) {
                echo Logger::getInstance(__CLASS__)->error('Application (Http): render content failure', __FILE__, __LINE__, $e);
            }
        }

        Profiler::setPoint($actionClass, $startTime, $startMemory);

        Logger::fb(Profiler::getReport(__CLASS__), __CLASS__, 'LOG');

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