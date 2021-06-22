<?php
/**
 * Ice core action abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\App;
use Ice\DataProvider\Registry;
use Ice\DataProvider\Repository;
use Ice\Exception\Config_Error;
use Ice\Exception\Console_Run;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Http;
use Ice\Exception\Http_Redirect;
use Ice\Exception\Http_Unauthorized;
use Ice\Helper\Access;
use Ice\Helper\Console as Helper_Console;
use Ice\Helper\Input;
use Ice\Helper\Json;

/**
 * Class Action
 *
 * Core action abstract class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Action
{
    use Stored;
    use Configured;

    /**
     * Child Actions
     *
     * Will be runned after current action
     *
     * @var array
     */
    private $actions = [];

    /**
     * Input params
     *
     * @var array
     */
    private $input = null;

    /**
     * Cache ttl
     *
     * @var null
     */
    private $ttl = null;


    private $result = null;

    /**
     * Private constructor of action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Return action registry
     *
     * @return Registry|DataProvider
     *
     * @throws Exception
     * @version 0.5
     * @since   0.5
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function getRegistry()
    {
        return Registry::getInstance(__CLASS__, self::getClass());
    }

    /**
     * @param array $params
     * @param int $level
     * @param bool $background
     * @param array $options
     * @return null
     * @throws Config_Error
     * @throws Console_Run
     * @throws Error
     * @throws Exception
     * @throws Http
     * @throws Http_Redirect
     * @throws FileNotFound
     */
    public static function call(array $params = [], $level = 0, $background = false, array $options = [])
    {
        if ($background) {
            array_walk($params, static function (&$value, $key) {
                return $value = $key . '=' . (trim($value) !== '' ? escapeshellarg(trim($value)) : '');
            });

            $commands = ICE_DIR . 'bin/ice ' . escapeshellarg(get_called_class()) . ' ' . implode(' ', $params);

            Helper_Console::run($commands, true, true);

            return [];
        }

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $logger = Logger::getInstance(__CLASS__);

        /** @var Action $actionClass */
        $actionClass = self::getClass();

        if (Request::isCli()) {
            $logger->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE, true);
        }

//        if ($actionClass::getConfig()->get('cache/ttl') != -1) {
//            $actionCacher = Action::getCacher($actionClass);
//        }

        $action = $actionClass::create($params, $options);

        $inputString = Json::encode($action->getInput());

        $hash = crc32($inputString);
        $actionHash = $actionClass . '/' . $hash;

        App::getContext()->initAction($actionClass, $hash);

//        if (isset($actionCacher) && $act = $actionCacher->get($actionHash)) {
//            return $act->result;
//        }

        $action->result = $action->transaction(
            function ($action) {
                return (array)$action->run($action->getInput());
            }
        );

        $key = 'run - ' . $actionClass . '/' . $hash;

        Profiler::setPoint($key, $startTime, $startMemory);

        Logger::fb($params, 'action: call ' . $actionClass . '/' . $hash, 'INFO');
        Logger::fb(Profiler::getReport($key), 'action', 'INFO');
        //            if ($content = $actionContext->getContent()) {
        //                App::getContext()->setContent(null);
        //                return $content;
        //            }

        $startTimeAfter = Profiler::getMicrotime();
        $startMemoryAfter = Profiler::getMemoryGetUsage();

        $rawActions = array_merge($action->actions, $actionClass::getConfig()->gets('actions', []));

        /**
         * @var string $actionKey
         * @var array $actionData
         * @var Action $subActionClass
         * @var array $subActionParams
         */
        foreach ($action->getActions($rawActions) as $actionKey => $actionData) {
            $newLevel = $level + 1;

            foreach ($actionData as $actionItem) {
                list($subActionClass, $subActionParams) = $actionItem;

                $result = [];

                try {
                    $result = $subActionClass::call($subActionParams, $newLevel);
                } catch (Http_Redirect $e) {
                    throw $e;
                } catch (Http $e) {
                    throw $e;
                } catch (\Exception $e) {
                    $result['error'] = $logger->error(
                        ['Calling subAction "{$0}" in action "{$1}" failed', [$subActionClass, $actionClass]],
                        __FILE__,
                        __LINE__,
                        $e
                    );
                }

                $action->result[$actionKey][] = $result;
            }
        }

        Profiler::setPoint('Action ' . $actionClass . ' (childs)', $startTimeAfter, $startMemoryAfter);

//        if (isset($actionCacher)) {
//            $actionCacher->set([$actionHash => $action], $action->getTtl());
//        }

        if (Request::isCli()) {
            $logger->info(
                ['{$0}{$1} complete!', [str_repeat("\t", $level), $actionClass::getClassName()]],
                Logger::MESSAGE,
                true
            );
        }

        return $action->result;
    }

    /**
     * Get action object by name
     * @param array $params
     * @return Action
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    private static function create(array $params = [], array $options = [])
    {
        $actionClass = self::getClass();

        /** @var Action $action */
        $action = new $actionClass();

        $action->init($params, $options);

        return $action;
    }

    /**
     * @param array $data
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    protected function init(array $data, array $options = [])
    {
        /** @var Action|Configured $actionClass */
        $actionClass = get_class($this);

        $config = $actionClass::getConfig();

//        $this->setInput(Input::get($config->gets('input', []), $data, $actionClass)); // use this ensteed next
        $this->initInput($config->gets('input', []), $data);

        $env = isset($this->input['env'])
            ? $this->input['env']
            : $config->get('access/env', false);

        $request = isset($this->input['request'])
            ? $this->input['request']
            : $config->get('access/request', false);

//        $roles = isset($this->input['roles'])
//            ? $this->input['roles']
//            : $actionClass::getConfig()->gets('access/roles', []);


        $checkAuth = $config->get('access/auth', null);

        if ($checkAuth !== null) {
            if (!Security::getInstance()->isAuth() && $checkAuth) {
                throw new Http_Unauthorized();
            }
        }

        $roles = $config->gets('access/roles', []);

        if (!empty($options['access']['roles'])) {
            $roles = array_merge($roles, (array)$options['access']['roles']);
        }

        if (Request::isCli()) {
            $roles = [];
        }

        Access::check(['env' => $env, 'roles' => $roles, 'request' => $request]);

        $this->initActions();
        $this->initTtl();
    }

    /**
     * @param array $configInput
     * @param array $data
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @deprecated use before in init (*)
     */
    protected function initInput(array $configInput, array $data = [])
    {
        /** @var Action|Configured $actionClass */
        $actionClass = get_class($this);

        $extendFields = ['actions', 'template', 'layout', 'viewRenderClass', 'env'];

        $configInput = array_merge(
            $actionClass::getConfig()->gets('input', []),
            $configInput, ['actions', 'template', 'layout', 'viewRenderClass', 'env']
        );

        $input = Input::get($configInput, $data);

        foreach ($extendFields as $extendField) {
            if ($input[$extendField] === null) {
                unset($input[$extendField]);
            }
        }

        $this->setInput($input);
    }

    private function initActions()
    {
        $input = $this->getInput();

        if (isset($input['actions'])) {
            foreach ((array)$input['actions'] as $key => $action) {
                if (is_string($action)) {
                    $action = [$key => $action];
                }

                $this->addAction($action);
            }

            unset($input['actions']);

            $this->setInput($input);
        }
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param array $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    protected function addAction(array $action)
    {
        $this->actions[] = $action;
    }

    private function initTtl()
    {
        $input = $this->getInput();

        if (isset($input['cache']['ttl'])) {
            $this->setTtl($input['cache']['ttl']);

            unset($input['cache']['ttl']);

            $this->setInput($input);
        } else {
            $this->setTtl(null);
        }
    }

    private function transaction($callback)
    {
        $result = null;

        try {
            $this->transactionBegin();

            $result = $callback($this);

            $this->transactionCommit();
        } catch (Exception $e) {
            $this->transactionRollback();

            throw $e;
        }

        return $result;
    }

    public function transactionBegin()
    {
        foreach ($this->getConfigData('dataSources', []) as $dataSourceName => $dataSourceData) {
            if (!empty($dataSourceData['transaction'])) {
                $this->getDataSource($dataSourceName)->beginTransaction();
            }
        }
    }

    public function getConfigData($configPart, $default)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        return is_array($default)
            ? $actionClass::getConfig()->gets($configPart, $default)
            : $actionClass::getConfig()->get($configPart, $default);
    }

    /**
     * @param $dataSourceName
     * @return \Ice\Core|Container|DataSource
     * @throws Exception
     */
    protected function getDataSource($dataSourceName)
    {
        return DataSource::getInstance(
            $this->getConfigData('dataSources/' . $dataSourceName . '/classKey', null)
        );
    }

    public function transactionCommit()
    {
        foreach ($this->getConfigData('dataSources', []) as $dataSourceName => $dataSourceData) {
            if (!empty($dataSourceData['transaction'])) {
                $this->getDataSource($dataSourceName)->commitTransaction();
            }
        }
    }

    public function transactionRollback()
    {
        foreach ($this->getConfigData('dataSources', []) as $dataSourceName => $dataSourceData) {
            if (!empty($dataSourceData['transaction'])) {
                $this->getDataSource($dataSourceName)->rollbackTransaction(new Error('Transaction rollback'));
            }
        }
    }

    /**
     * @param array $rawActions
     * @return array
     */
    public function getActions(array $rawActions)
    {
        $actions = [];

        foreach ($rawActions as $action) {
            if (empty($action)) {
                continue;
            }

            list($class, $params) = array_pad((array)$action, 2, []);

            $class = self::getClass($class, $this);

            $actions[$class::getClassName()][] = [$class, $params];
        }

        return $actions;
    }

    /**
     * Return action repository
     *
     * @return Repository
     *
     * @throws Exception
     * @version 0.5
     * @since   0.5
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function getRepository()
    {
        return Repository::getInstance(__CLASS__, self::getClass());
    }

    /**
     * Action config
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => null, 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
            'dataSources' => []
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     */
    abstract public function run(array $input);

    /**
     * @return null
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set action result cache ttl
     *
     * @param integer $ttl
     *
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    protected function setTtl($ttl)
    {
        if ($ttl === null) {
            /**
             * @var Action $actionClass
             */
            $actionClass = get_class($this);
            $ttl = $actionClass::getConfig()->get('cache/ttl', false);
        }

        $this->ttl = $ttl;
    }

    /**
     * @param array $output
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public function setOutput($output)
    {
        foreach (self::getConfig()->gets('output', []) as $name => $dataProviderKey) {
            if (!isset($output[$name])) {
                $output[$name] = DataProvider::getInstance($dataProviderKey)->get($name);
            }
        }

        $this->getView()->setData($output);
    }

    /**
     * Validate cacheable object
     *
     * @return array
     *
     * @throws Exception
     * @version 0
     * @since   0
     * @author anonymous <email>
     *
     */
    public function validate()
    {
        return Validator::validateParams($this->getInput(), $this->getInputConfig());
    }

    /**
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated 1.10 use Action::getConfigData
     * @version 1.9
     * @since   1.9
     */
    public function getInputConfig()
    {
        return $this->getConfigData('input', []);
    }

    /**
     * Invalidate cacheable object
     *
     * @return Action
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function invalidate()
    {
        return $this;
    }

    /**
     * @return Logger
     * @throws Error
     * @throws FileNotFound
     */
    public function getLogger()
    {
        return Logger::getInstance(get_class($this));
    }

    /**
     * @param DataProvider|string $dataProviderClass
     * @param string $key
     * @param string $index
     * @return DataProvider
     *
     * @throws Exception
     * @todo Need rename to getDataProvider
     */
    public function getDProvider($dataProviderClass, $key = 'default', $index = null)
    {
        if (!$index) {
            $index = get_class($this);
        }

        return $dataProviderClass::getInstance($key, $index);
    }

    public function transacionRestart($callback = null)
    {
        $this->transactionRollback();

        if (is_callable($callback)) {
            $callback();
        }

        $this->transactionBegin();
    }
}
