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
use Ice\Exception\Http;
use Ice\Exception\Redirect;
use Ice\Helper\Access;
use Ice\Helper\Input;
use Ice\Helper\Json;

/**
 * Class Action
 *
 * Core action abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Action implements Cacheable
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
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRegistry()
    {
        return Registry::getInstance(__CLASS__, self::getClass());
    }

    public static function call(array $params = [], $level = 0)
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $logger = Logger::getInstance(__CLASS__);

        /**
         * @var Action $actionClass
         */
        $actionClass = self::getClass();

        if (Request::isCli()) {
            $logger->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE, true);
        }

        if ($actionClass::getConfig()->get('cache/ttl') != -1) {
            $actionCacher = Action::getCacher($actionClass);
        }

        $action = $actionClass::create($params);

        $inputString = Json::encode($action->getInput());

        $hash = crc32($inputString);
        $actionHash = $actionClass . '/' . $hash;

        App::getContext()->initAction($actionClass, $hash);

        if (isset($actionCacher) && $act = $actionCacher->get($actionHash)) {
            return $act->result;
        }

        $action->result = (array)$action->run($action->getInput());

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

            foreach ($actionData as $subActionClass => $actionItem) {
                /**@var Action $subActionClass */
                list($subActionClass, $subActionParams) = each($actionItem);

                $result = [];

                try {
                    $result = $subActionClass::call($subActionParams, $newLevel);
                } catch (Redirect $e) {
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

        if (isset($actionCacher)) {
            $actionCacher->set([$actionHash => $action], $action->getTtl());
        }

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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function create(array $params = [])
    {
        $actionClass = self::getClass();

        /** @var Action $action */
        $action = new $actionClass();

        $action->init($params);

        return $action;
    }

    protected function init(array $data = [])
    {
        /** @var Action|Configured $actionClass */
        $actionClass = get_class($this);

        $this->initInput($actionClass::getConfig()->gets('input', []), $data);

        $env = isset($this->input['env'])
            ? $this->input['env']
            : $actionClass::getConfig()->get('access/env', false);

        $request = isset($this->input['request'])
            ? $this->input['request']
            : $actionClass::getConfig()->get('access/request', false);

        $roles = isset($this->input['roles'])
            ? $this->input['roles']
            : $actionClass::getConfig()->get('access/roles', false);

        Access::check(['env' => $env, 'roles' => $roles, 'request' => $request]);

        $this->initActions();
        $this->initTtl();
    }

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

        if (isset($input['ttl'])) {
            $this->setTtl($input['ttl']);

            unset($input['ttl']);

            $this->setInput($input);
        } else {
            $this->setTtl(null);
        }
    }

    /**
     * Action config
     *
     * @return array|void
     *
     *  protected static function config()
     *  {
     *      return [
     *          'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
     *          'cache' => ['ttl' => -1, 'count' => 1000],
     *          'actions' => [],
     *          'input' => [],
     *          'output' => []
     *      ];
     *  }
     *
     * /** Run action
     *
     * @param  array $input
     * @return array
     */
    abstract public function run(array $input);

    /**
     * @param array $rawActions
     * @return array
     */
    public function getActions(array $rawActions)
    {
        $actions = [];

        foreach ($rawActions as $key => $action) {
            if (empty($action)) {
                continue;
            }

            list($key, $class, $params) = $this->prepareAction($key, $action);

            if (empty($key) || is_int($key)) {
                $key = $class::getClassName();
            }

            $actions[$key][] = [$class => $params];
        }

        return $actions;
    }

    private function prepareAction($key, $action)
    {
        $params = [];

        if (is_array($action)) {
            list($class, $key) = each($action);

            if (is_int($class)) {
                $class = $key;
                $key = 0;
            }

            $params = count($action) < 2 ? [] : current($action);
        } else {
            if (!is_int($key)) {
                $class = $key;
                $key = $action;
            } else {
                $class = $action;
            }
        }

        return [$key, Action::getClass($class, $this), $params];
    }

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
            $ttl = $actionClass::getConfig()->get('ttl', false);
        }

        $this->ttl = $ttl;
    }

    /**
     * Return action repository
     *
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
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
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    /**
     * @param array $output
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
     * @param  $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function validate($value)
    {
        return $this;
    }

    /**
     * Invalidate cacheable object
     *
     * @return Cacheable
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

    public function getLogger()
    {
        return Logger::getInstance(get_class($this));
    }
}
