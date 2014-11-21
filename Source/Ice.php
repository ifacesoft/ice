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
use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Container;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Helper\Memory;
use Ice\Helper\Object;

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
        return new Ice($moduleName);
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
            /** @var Action $action */
            $action = Request::isCli()
                ? Front_Cli::getInstance()
                : (Request::isAjax()
                    ? Front_Ajax::getInstance()
                    : Front::getInstance());

            Response::send($action->call(new Action_Context()));
        } catch (\Exception $e) {
            Ice::getLogger()->error('Application failure', __FILE__, __LINE__, $e);
        }

        return $this;
    }

    /**
     * Flush ice application
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function flush()
    {
        if (!Environment::isProduction()) {
            Logger::renderLog();

            if (function_exists('fb')) {
                fb('running time: ' . Logger::microtimeResult($this->_startTime) * 1000 . ' ms | ' . Memory::memoryGetUsagePeak(), 'INFO');
            }
        }

        if (!Request::isCli() && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
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
    public static function get($class) {
        return $class::getInstance();
    }
}
