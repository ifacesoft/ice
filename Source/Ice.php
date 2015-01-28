<?php
/**
 * Ice application class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

use Ice\Action\Front;
use Ice\Action\Front_Ajax;
use Ice\Action\Front_Cli;
use Ice\Action\Http_Status;
use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Container;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Request;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Memory;

/**
 * Class Ice
 *
 * Run and flush ice application
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */
class Ice extends Container
{
    use Core;

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
     * @param $hash string hash md5
     * @return Ice
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($moduleName, $hash = null)
    {
        return new Ice($moduleName, $hash);
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
        $actionContext = Action_Context::create();

        try {
            /** @var Action $action */
            $action = Request::isCli()
                ? Front_Cli::getInstance()
                : (Request::isAjax()
                    ? Front_Ajax::getInstance()
                    : Front::getInstance());

            $actionContext->getResponse()->setContent($action->call($actionContext));
        } catch (Redirect $e) {
            $actionContext->getResponse()->setRedirectUrl($e->getMessage());
        } catch (Http_Bad_Request $e) {
            $actionContext->getResponse()->setContent(Http_Status::getInstance()->call($actionContext, ['code' => 400, 'exception' => $e]));
        } catch (Http_Not_Found $e) {
            $actionContext->getResponse()->setContent(Http_Status::getInstance()->call($actionContext, ['code' => 404, 'exception' => $e]));
        } catch (\Exception $e) {
            $actionContext->getResponse()->setContent(Http_Status::getInstance()->call($actionContext, ['code' => 500, 'exception' => $e]));
        }

        $actionContext->getResponse()->send();

        if (!Environment::isProduction()) {
            Logger::renderLog();
            Logger::fb('running time: ' . Logger::microtimeResult($this->_startTime) . ' | ' . Memory::memoryGetUsagePeak(), 'INFO');
        }

        if (!Request::isCli() && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
