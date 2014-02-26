<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 27.10.13
 * Time: 15:13
 */

namespace ice\core;

use ice\core\helper\Json;
use ice\core\helper\Object;
use ice\Exception;
use ice\Ice;

abstract class Action
{
    /**
     * Переопределяемый конфиг
     *
     * @var array
     */
    public static $config = array();

    /**
     * @var array
     */
    private static $callStack = array();

    protected $staticActions = array();

    protected $layout = null;

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @var array
     */
    private $_config = null;

    protected $inputDefaults = array();

    /**
     * Приватный конструктор. Создаем через Action::create()
     */
    private function __construct()
    {
    }

    /**
     * @param array $data
     * @param int $level
     * @return View
     * @throws Exception
     */
    public static function call(array $data = array(), $level = 0)
    {
        $action = null;

        /** @var Action $actionClass */
        $actionClass = get_called_class();

        try {
            $timePoint = null;

            /** @var Action $action */
            $action = $actionClass::get();

            $actionContext = new Action_Context($actionClass, $action->getStaticActions(), $action->getLayout());

            $action->init($actionContext);

            $input = array_merge($action->getInput($actionContext->getDataProviderKeys()), $data);

            self::pushToCallStack($actionClass, $input);

            $actionContext->setData((array)$action->run($input, $actionContext));

            foreach ($actionContext->getActions() as $subActionClass => $actionData) {
                $level += 1;
                $data = array();

                foreach ($actionData as $subActionKey => $subActionParams) {
                    $data[$subActionKey] = $subActionClass::call($subActionParams, $level);
                }

                $actionContext->assign($subActionClass, $data);
            }

            $action->flush($actionContext);

        } catch (\Exception $e) {
            throw new Exception('Не удалось вызвать экшин "' . $actionClass . '"', array(), $e);
        }

        return $actionContext->getView();
    }

    /**
     * @throws Exception
     * @return Action
     */
    public static function get()
    {
        /** @var Action $actionClassName */
        $actionClassName = get_called_class();

        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(
            Ice::getConfig()->getParam('actionDataProviderKey') . $actionClassName
        );

        /** @var Action $action */
        $action = $dataProvider->get($actionClassName);

        if ($action) {
            return $action;
        }

        $action = $actionClassName::create();

        if (!$action) {
            throw new Exception('Could not create action "' . $actionClassName . '"');
        }

        $dataProvider->set($actionClassName, $action);

        return $action;
    }

    /**
     * @return Action
     */
    private static function create()
    {
        $actionClassName = get_called_class();

        return new $actionClassName();
    }

    /**
     * @param Action_Context $actionContext
     */
    protected function init(Action_Context &$actionContext)
    {
    }

    private function getInput($dataProviderKeys)
    {
        $input = array();

        /** @var Data_Provider $dataProvider */
        $dataProvider = null;

        foreach ($dataProviderKeys as $dataProviderKey) {
            $dataProvider = Data_Provider::getInstance($dataProviderKey);
            $input = array_merge($input, (array)$dataProvider->get());
        }

        foreach ($this->getInputDefaults() as $param => $value) {
            if (empty($input[$param])) {
                $input[$param] = $value;
            }
        }

        return $input;
    }

    protected function getInputDefaults()
    {
        $config = $this->getConfig();

        if (!$config) {
            return $this->inputDefaults;
        }

        if (!empty($config->getParams('inputDefaults', false))) {
            $this->inputDefaults = array_merge($this->inputDefaults, $this->getConfig()->getParams('inputDefaults'));
        }

        return $this->inputDefaults;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if ($this->_config !== null) {
            return $this->_config;
        }

        $className = $this->getClass();

        $this->_config = Config::get($className, $className::$config);

        return $this->_config;
    }

    public static function getClass()
    {
        return get_called_class();
    }

    private static function pushToCallStack($actionClass, $input)
    {
        $actionName = Object::getName($actionClass);
        $inputJson = Json::encode($input);

        if (!isset(self::$callStack[$actionName])) {
            self::$callStack[$actionName] = array();
        }

        if (in_array($inputJson, self::$callStack[$actionName])) {
            throw new Exception('action "' . $actionName . '" with input ' . $inputJson . ' already runned. May by found infinite loop.');
        }

        self::$callStack[$actionName][] = $inputJson;
    }

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    abstract protected function run(array $input, Action_Context &$actionContext);

    /**
     * @param Action_Context $context
     */
    protected function flush(Action_Context &$context)
    {
    }

    public static function getCallStack()
    {
        return self::$callStack;
    }

    public static function getHash($data)
    {
        return (hash('crc32b', igbinary_serialize($data)));
    }

    private function getStaticActions()
    {
        return $this->staticActions;
    }
}