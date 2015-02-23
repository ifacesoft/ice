<?php
/**
 * Ice core action abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Cli as Data_Provider_Cli;
use Ice\Data\Provider\Registry;
use Ice\Data\Provider\Repository;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Router as Data_Provider_Router;
use Ice\Data\Provider\Session as Data_Provider_Session;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Helper\Json;
use Ice\Helper\Php;
use Ice\Helper\Validator as Helper_Validator;

/**
 * Class Action
 *
 * Core action abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
abstract class Action implements Cacheable
{
    use Core;

    /**
     * Child Actions
     *
     * Will be runned after current action
     *
     * @var array
     */
    private $_actions = [];
    private $_template = null;
    private $_input = [];
    private $_output = [];
    private $_viewRenderClass = null;
    private $_layout = null;
    private $_ttl = null;

    /**
     * Private constructor of action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
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
     * @since 0.5
     */
    public static function getRegistry()
    {
        return Registry::getInstance(__CLASS__, self::getClass());
    }

    /**
     * Get action object by name
     *
     * @return Action
     * @throws Redirect
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function create()
    {
        $actionClass = self::getClass();

        return new $actionClass();
    }

    /**
     * @param array $data
     * @return array
     * @throws Redirect
     */
    public static function getInput(array $data = [])
    {
        $dataProviderKeyMap = [
            'request' => Data_Provider_Request::DEFAULT_DATA_PROVIDER_KEY,
            'router' => Data_Provider_Router::DEFAULT_DATA_PROVIDER_KEY,
            'session' => Data_Provider_Session::DEFAULT_DATA_PROVIDER_KEY,
            'cli' => Data_Provider_Cli::DEFAULT_DATA_PROVIDER_KEY,
        ];

        $input = [];
        foreach (self::getConfig()->gets('input', false) as $name => $param) {
            if (is_int($name)) {
                $name = $param;
                $param = [];
            }

            $dataProviderKeys = isset($param['providers'])
                ? (array)$param['providers']
                : ['default'];

            foreach ($dataProviderKeys as $dataProviderKey) {
                if (isset($input[$name])) {
                    continue;
                }

                if (isset($dataProviderKeyMap[$dataProviderKey])) {
                    $dataProviderKey = $dataProviderKeyMap[$dataProviderKey];
                }

                $input[$name] = $dataProviderKey == 'default'
                    ? (isset($data[$name]) ? $data[$name] : null)
                    : Data_Provider::getInstance($dataProviderKey)->get($name);
            }

            $input[$name] = Action::getInputParam($name, $input[$name], $param);
        }

        if (isset($input['redirectUrl'])) {
            throw new Redirect($input['redirectUrl']);
        }

        return $input;
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getConfig()
    {
        $repository = self::getRepository();

        if ($config = $repository->get('config')) {
            return $config;
        }

        $actionClass = self::getClass();

        $config = Config::create($actionClass, array_merge_recursive($actionClass::config(), Config::getInstance($actionClass, null, false, -1)->gets()));

        return $repository->set('config', $config);
    }

    /**
     * Return action repository
     *
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getRepository()
    {
        return Repository::getInstance(__CLASS__, self::getClass());
    }

    /**
     * Return valid input value
     *
     * @param $name
     * @param $value
     * @param $param
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getInputParam($name, $value, $param)
    {
        if (empty($param)) {
            return $value;
        }

        if ($value === null && isset($param['default'])) {
            $value = $param['default'];
        }

        if (isset($param['type'])) {
            $value = Php::castTo($param['type'], $value);
        }

        if (isset($param['validators'])) {
            foreach ((array)$param['validators'] as $validatorClass => $validatorParams) {
                if (is_int($validatorClass)) {
                    $validatorClass = $validatorParams;
                    $validatorParams = null;
                }

                Helper_Validator::validate($validatorClass, $validatorParams, $name, $value);
            }
        }

//        if (isset($param['converter']) && is_callable($param['converter'])) {
//            $value = $param['converter']($value);
//        }

        return $value;
    }

    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [];
    }

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     *
     *  protected static function config()
     *  {
     *      return array_merge_recursive(
     *          [
     *             'actions' => [],
     *              'view' => [
     *                  'layout' => null,
     *                  'template' => null,
     *                  'viewRenderClass' => null,
     *              ],
     *              'input' => [],
     *              'output' => [],
     *              'ttl' => 3600,
     *              'roles' => []
     *          ],
     *          parent::config()
     *      );
     *  }
     *
     * /** Run action
     *
     * @param array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function run(array $input);

    protected function addAction(array $action)
    {
        $this->_actions[] = $action;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        $actions = [];

        /** @var Action $actionClass */
        $actionClass = get_class($this);

        foreach (array_merge($this->_actions, $actionClass::getConfig()->gets('actions', false)) as $key => $action) {
            $params = [];

            if (is_string($action)) {
                $action = [$key => $action];
            }

            list($key, $class) = each($action);

            if (count($action) > 1) {
                $params = current($action);
            }

            $class = $class[0] == '_'
                ? get_class($this) . $class
                : Action::getClass($class);

            if (!empty($key) && is_string($key)) {
                $actions[$key] = [$class => $params];
            } else {
                $actions[] = [$class => $params];
            }

        }

        return $actions;
    }

    /**
     * @return null
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set view template
     *
     * @param string $template
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function setTemplate($template)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        if ($template === null) {
            $template = $actionClass::getConfig()->get('view/template', false);

            if ($template === null) {
                $this->_template = $actionClass;
                return;
            }
        }

        if ($template === '') {
            $this->_template = $template;
            return;
        }

        if ($template[0] == '_') {
            $this->_template = $actionClass . $template;
            return;
        }

        $this->_template = $template;
    }

    /**
     * Init input params
     *
     * @param array $input
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function setInput($input)
    {
        $this->initActions($input);
        $this->initTemplate($input);
        $this->initViewRenderClass($input);
        $this->initLayout($input);
        $this->initTtl($input);

        $this->_input = $input;
    }

    private function initActions(&$input)
    {
        if (isset($input['actions'])) {
            foreach ((array)$input['actions'] as $key => $action) {
                if (is_string($action)) {
                    $action = [$key => $action];
                }

                $this->addAction($action);
            }

            unset($input['actions']);
        }
    }

    private function initTemplate(&$input)
    {
        if (isset($input['template'])) {
            $this->setTemplate($input['template']);
            unset($input['template']);
        } else {
            $this->setTemplate(null);
        }
    }

    private function initViewRenderClass(&$input)
    {
        if (isset($input['viewRenderClass'])) {
            $this->setViewRenderClass($input['viewRenderClass']);
            unset($input['viewRenderClass']);
        } else {
            $this->setViewRenderClass(null);
        }
    }

    private function initLayout(&$input)
    {
        if (isset($input['layout'])) {
            $this->setLayout($input['layout']);
            unset($input['layout']);
        } else {
            $this->setLayout(null);
        }
    }

    private function initTtl(&$input)
    {
        if (isset($input['ttl'])) {
            $this->setTtl($input['ttl']);
            unset($input['ttl']);
        } else {
            $this->setTtl(null);
        }
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * @param array $output
     */
    public function setOutput($output)
    {
        foreach (self::getConfig()->gets('output', false) as $name => $dataProviderKey) {
            if (!isset($output[$name])) {
                $output[$name] = Data_Provider::getInstance($dataProviderKey)->get($name);
            }
        }

        $this->_output = $output;
    }

    /**
     * @return null
     */
    public function getViewRenderClass()
    {
        return $this->_viewRenderClass;
    }

    /**
     * Set view render class
     *
     * @param string $viewRenderClass
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function setViewRenderClass($viewRenderClass)
    {
        if (!$viewRenderClass) {
            /** @var Action $actionClass */
            $actionClass = get_class($this);
            $viewRenderClass = $actionClass::getConfig()->get('view/viewRenderClass', false);
        }

        $this->_viewRenderClass = View_Render::getClass($viewRenderClass);
    }

    /**
     * @return null
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Set emmet view layout
     *
     * @param string $layout
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function setLayout($layout)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        if ($layout === null) {
            $layout = $actionClass::getConfig()->get('view/layout', false);

            if ($layout === null) {
                $this->_layout = 'div.' . $actionClass::getClassName();
                return;
            }
        }

        if ($layout === '') {
            $this->_layout = $layout;
            return;
        }

        if ($layout[0] == '_') {
            $this->_layout = 'div.' . $actionClass::getClassName() . $layout;
            return;
        }

        $this->_layout = $layout;
    }

    /**
     * @return null
     */
    public function getTtl()
    {
        return $this->_ttl;
    }

    /**
     * Set action result cache ttl
     *
     * @param integer $ttl
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function setTtl($ttl)
    {
        if ($ttl === null) {
            /** @var Action $actionClass */
            $actionClass = get_class($this);
            $ttl = $actionClass::getConfig()->get('ttl', false);
        }

        $this->_ttl = $ttl;
    }


    /**
     * Validate cacheable object
     *
     * @param $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
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
     * @since 0
     */
    public function invalidate()
    {
        return $this;
    }

    /**
     * Restore object
     *
     * @param array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function __set_state(array $data)
    {
        $class = self::getClass();

        $object = new $class();

        foreach ($data as $fieldName => $fieldValue) {
            $object->$fieldName = $fieldValue;
        }

        return $object;
    }
}