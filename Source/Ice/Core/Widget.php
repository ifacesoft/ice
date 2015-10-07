<?php
namespace Ice\Core;

use Ice\Exception\Access_Denied;
use Ice\Helper\Access;
use Ice\Helper\Arrays;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\Render\Php;
use Ice\Render\Replace;
use Ice\Widget\Resource_Dynamic;
use Ice\Action\Render as Core_Render;

abstract class Widget extends Container
{
    use Stored;
    use Configured;

    /**
     * @var array rows of values
     */
    private $rows = [[]];
    private $values = [];

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * Widget parts (fields for Form, columns for Data, items for Menu)
     *
     * @var array
     */
    protected $parts = [];
    private $classes = '';

    private $dataParams = null;
    private $dataAction = null;

    private $output = null;

    private $token = null;
    private $data = [];

    private $result = null;
    private $compiledResult = null;

    private $parentWidgetId = null;
    /**
     * Redirect url
     *
     * If is null use referrer url
     *
     * @var string|null
     */
    private $redirect = null;

    /**
     * Timeout redirect after success registration
     *
     * @var int
     */
    private $timeout = 0;

    /**
     * Not ignored parts
     *
     * @var array
     */
    private $filterParts = [];

    /**
     * Default options
     *
     * @todo replace to method config
     *
     * @var array
     */
    protected $defaultOptions = [
        'disabled' => false,
        'readonly' => false,
        'required' => false,
        'autofocus' => false,
        'input' => []
    ];

    private $resource = null;
    private $template = null;

    /**
     * @param string $attributes
     * @param bool|false $force
     * @return string|null
     */
    public function getLayout($attributes = '', $force = false)
    {
        $layout = null;

        /** @var Configured $class */
        $class = get_called_class();

        if (!$force) {
            $layout = $class::getConfig()->get('render/layout');
        }

        if (!$layout) {
            return null;
        }

        if ($layout === true) {
            return 'div.' . Object::getClassName($class) . $attributes;
        }

        if ($layout[0] == '_') {
            return 'div.' . Object::getClassName($class) . $layout . $attributes;
        }

        return $layout;
    }

    /**
     * @return string|null
     */
    private function getTemplate()
    {
        if ($this->template !== null) {
            return $this->template;
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $this->setTemplate($widgetClass::getConfig()->get('render/template'));
    }

    /**
     * @param string $template
     * @return null|string
     */
    public function setTemplate($template)
    {
        if (empty($template)) {
            return $this->template = $template === '' ? 'Ice\Widget\Blank' : 'Ice\Widget\Default';
        }

        if ($template === true) {
            return $this->template = get_class($this);
        }

        if ($template[0] == '_') {
            return $this->template = get_class($this) . $template;
        }

        return $this->template = $template;
    }

    /**
     * @param bool|false $force
     * @return Render
     */
    public function getRender($force = false)
    {
        $render = null;

        /** @var Configured $class */
        $class = get_called_class();

        if (!$force) {
            $render = $class::getConfig()->get('render/class');
        }

        if (!$render || $render === true) {
            $render = Config::getInstance(Render::getClass())->get('default');
        }

        /** @var Render $renderClass */
        $renderClass = Render::getClass($render);

        return $renderClass::getInstance();
    }

    public function setResource($resource, $force = false)
    {
        if ($resource instanceof Resource) {
            return $this->resource = $resource;
        }

        /** @var Configured $class */
        $class = get_called_class();

        if (!$resource && !$force) {
            $resource = $class::getConfig()->get('render/resource');
        }

        if (!$resource) {
            return null;
        }

        if ($resource === true || (is_array($resource) && !isset($resource['class']))) {
            $resource = $class;
        }

        if (is_array($resource)) {
            $resource = $resource['class'];
        }

        return $this->resource = Resource::create($resource);
    }

    /**
     * @param null $resource
     * @param bool|false $force
     * @return Resource
     */
    public function getResource($resource = null, $force = false)
    {
        if (!$force && $this->resource !== null) {
            return $this->resource;
        }

        return $this->setResource($resource);
    }

    protected function init(array $params)
    {
        $this->token = crc32(String::getRandomString());

        if (isset($params['parentWidgetId'])) {
            $this->parentWidgetId = $params['parentWidgetId'];
            unset($params['parentWidgetId']);
        }

        $this->bind(Input::get(get_class($this), $params));
        $this->loadResource();
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $this->output = (array)$this->build($this->values);

        $key = 'build - ' . get_class($this) . '/' . $this->getInstanceKey();

        Profiler::setPoint($key, $startTime, $startMemory);

        Logger::fb(Profiler::getReport($key), 'widget', 'INFO');
    }

    /**
     * Widget config
     *
     * @return array
     *
     *  protected static function config()
     *  {
     *      return [
     *          'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
     *          'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
     *          'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
     *          'cache' => ['ttl' => -1, 'count' => 1000],
     *          'input' => [],
     *          'output' => [],
     *          'action' => [
     *          //  'class' => 'Ice:Render',
     *          //  'params' => [
     *          //      'widgets' => [
     *          ////        'Widget_id' => Widget::class
     *          //      ]
     *          //  ],
     *          //  'url' => true,
     *          //  'method' => 'POST',
     *          //  'callback' => null
     *          ]
     *      ];
     *  }
     *
     * /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected abstract function build(array $input);

    /**
     * @param string $key
     * @param null $ttl
     * @param array $params
     * @return $this
     */
    public static function getInstance($key, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $classes
     *
     * @return $this
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @param array $rows
     * @return $this
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bind(array $params)
    {
        $this->values = array_merge($this->values, $params);
        return $this;
    }

    public function setQueryResult(Query_Result $queryResult)
    {
        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->setQueryResult($queryResult);
            }
        }
    }

    public function queryBuilderPart(Query_Builder $queryBuilder, array $input)
    {
        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->queryBuilderPart($queryBuilder, $input);
            }
        }
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage() . ' - ' . $this->getClass() . ' (' . $e->getFile() . ':' . $e->getLine() . ')<br>' .
            nl2br($e->getTraceAsString());
        }
    }

    public function getFullUrl($url)
    {
        $queryString = http_build_query($this->getDataParams());

        return $url . ($queryString ? '?' . $queryString : '');
    }

    public function getResult()
    {
        if ($this->result !== null) {
            return $this->result;
        }

        $this->result = [];

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetName = $this->getInstanceKey();

        $offset = $this->getOffset();

        foreach ($this->rows as $values) {
            if (empty($values)) {
                $values = $this->values;
            }

            $row = [];

            foreach ($this->getParts($this->getFilterParts()) as $partName => $part) {
                $part['widgetClassName'] = $widgetClassName;
                $part['widgetName'] = $widgetName;

                $part['resource'] = $this->getResource();

                $resourceParams = [];

                if (isset($part['options']['resource'])) {
                    $part['resource'] = $this->getResource($part['options']['resource']);

                    if (is_array($part['options']['resource'])) {
                        if (isset($part['options']['resource'][0])) {
                            $part['resource'] = Resource::create($part['options']['resource'][0]);
                            if (array_key_exists(1, $part['options']['resource'])) {
                                $resourceParams = $part['options']['resource'][1];
                            }
                        } else if (isset($part['options']['resource']['class'])) {
                            $part['resource'] = Resource::create($part['options']['resource']['class']);
                            $resourceParams = isset($part['options']['resource']['params'])
                                ? (array)$part['options']['resource']['params']
                                : [];
                        } else {
                            $resourceParams = isset($part['options']['resource']['params'])
                                ? (array)$part['options']['resource']['params']
                                : (array)$part['options']['resource'];
                        }
                    }

                    unset($part['options']['resource']);
                }

                $part['name'] = isset($part['options']['name']) ? $part['options']['name'] : $partName;
                $part['value'] = isset($part['options']['value']) ? $part['options']['value'] : $part['name'];

                $params = [];

                if (isset($part['options']['params'])) {
                    foreach ((array)$part['options']['params'] as $key => $param) {
                        if (is_array($param)) {
                            $param = Json::encode($param);
                        }

                        if (is_int($key)) {
                            $params[$param] = $values[$param];
                        } else {
                            $params[$key] = array_key_exists($param, $values) ? $values[$param] : $param;
                        }
                    }
                } else {
                    $params = [$part['name'] => isset($values[$part['name']]) ? $values[$part['name']] : null];
                }

                $part['params'] = $params;
                $part['dataParams'] = Json::encode($params);

                if (!empty($part['options']['route'])) {
                    if (is_array($part['options']['route'])) {
                        list($routeName, $routeParams) = each($part['options']['route']);

                        $routeParams = array_merge($part['params'], (array)$routeParams);
                    } else {
                        $routeParams = $part['params'];

                        $routeName = $part['options']['route'] === true
                            ? $partName
                            : $part['options']['route'];
                    }

                    if (!array_key_exists('href', $part['options'])) {
                        $part['options']['href'] = Router::getInstance()->getUrl($routeName, $routeParams);
                    }

                    if (!array_key_exists('active', $part['options'])) {
                        $part['options']['active'] = String::startsWith(Request::uri(), $part['options']['href']);
                    }

                    if (!array_key_exists('label', $part['options'])) {
                        $part['label'] = $routeName;
                        $part['options']['label'] = Resource::create(Route::getClass())->get($routeName, $routeParams);
                    }
                } else {
                    if (!array_key_exists('label', $part['options'])) {
                        if (isset($part['options']['template'])) {
                            $part['label'] = $part['options']['template'];

                            $template = $part['resource']
                                ? $part['resource']->get($part['label'], $resourceParams)
                                : $part['label'];

                            $part['options']['label'] =
                                Replace::getInstance()->fetch($template, $params, null, Render::TEMPLATE_TYPE_STRING);
                        } else {
                            $part['label'] = $part['name'];

                            $part['options']['label'] = $part['resource']
                                ? $part['resource']->get($part['label'], $resourceParams)
                                : $part['label'];
                        }
                    } else {
                        $part['label'] = $part['options']['label'];

                        if ($part['resource']) {
                            $part['options']['label'] = $part['resource']->get($part['label'], $resourceParams);
                        }
                    }

                    if ($part['resource'] && array_key_exists('placeholder', $part['options'])) {
                        $part['options']['placeholder'] = $part['resource']->get($part['options']['placeholder']);
                    }
                }

                $template = $part['template'][0] == '_'
                    ? $widgetClass . $part['template']
                    : $part['template'];

                $part['offset'] = $offset + 1;
                $part['content'] = Php::getInstance()->fetch($template, $part);

                $row[$partName] = $part;
            }

            $this->result[++$offset] = $row;
        }

        return $this->result;
    }

    public function walkOptions($function, $option)
    {
        array_walk($this->parts, $function, $option);
        return $this;
    }

    protected function getCompiledResult()
    {
        if ($this->compiledResult !== null) {
            return $this->compiledResult;
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();

        $dataAction = empty($this->getDataAction())
            ? []
            : array_intersect_key($this->getDataAction(), array_flip(['class', 'params', 'url', 'method']));

        return $this->compiledResult = array_merge(
            [
                'result' => $this->getResult(),
                'widgetName' => $this->getInstanceKey(),
                'widgetData' => $this->getData(),
                'widgetClassName' => $widgetClassName,
                'widgetResource' => $this->getResource(),
                'classes' => $this->getClasses(),
                'dataAction' => Json::encode($dataAction),
                'dataParams' => Json::encode($this->getDataParams()),
                'dataWidget' => Json::encode($this->getDataWidget()),
                'dataFor' => $this->getParentWidgetId(),
            ],
            (array)$this->output
        );
    }

    private function getDataWidget()
    {
        return [
            'class' => get_class($this),
            'name' => $this->getInstanceKey(),
            'token' => $this->getToken(),
            'resourceClass' => $this->getResource() ? $this->getResource()->getResourceClass() : null
        ];
    }

    public function render()
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $result = $this->getRender()
            ->fetch(
                $this->getTemplate(),
                $this->getCompiledResult(),
                $this->getLayout()
            );

        $key = 'render - ' . get_class($this) . '/' . $this->getInstanceKey();

        Profiler::setPoint($key, $startTime, $startMemory);

        Logger::fb(Profiler::getReport($key), 'widget', 'INFO');

        return $result;
    }

    public function getDataParams()
    {
        if ($this->dataParams !== null) {
            return $this->dataParams;
        }

        $this->dataParams = $this->getValues();

        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $this->dataParams = array_merge($part['options']['widget']->getDataParams(), $this->dataParams);
            }
        }

        return $this->dataParams;
    }

    /**
     * @param array $dataParams
     */
    public function setDataParams(array $dataParams)
    {
        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->setDataParams($dataParams);
            }
        }

        $this->dataParams = $dataParams;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function getValue($key)
    {
        return isset($this->getValues()[$key]) ? $this->getValues()[$key] : null;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $partName
     * @param array $options
     * @param $template
     * @param $element
     * @return $this
     */
    protected function addPart($partName, array $options, $template, $element)
    {
        try {
            if (isset($part['options']['access'])) {
                Access::check($part['options']['access']);
            }
        } catch (Access_Denied $e) {
            return $this;
        }

        if (!empty($options['rewrite']) && isset($this->parts[$partName])) {
            unset($this->parts[$partName]);
        }

        $widgetClass = get_class($this);

        $options = Arrays::defaults($this->defaultOptions, $options);

        if (isset($options['default']) && $this->getValue($partName) === null) {
            $this->bind([$partName => $options['default']]);
        }

        $dataActionArr = null;

        foreach (['onclick', 'onchange'] as $event) {
            if (array_key_exists($event, $options)) {
                $dataAction = $options[$event];

                if ($dataAction === true) {
                    if (empty($this->getDataAction())) {
                        $dataAction = [
                            'class' => Core_Render::class,
                            'params' => ['widgets' => [$this->getInstanceKey() => get_class($this)]],
                            'url' => Request::uri(true),
                            'method' => 'GET',
                            'callback' => null
                        ];

                        $dataActionArr = $dataAction;
                    } else {
                        $dataAction = $this->dataAction;
                    }
                } else {
                    $dataActionArr = $dataAction;
                }

                $options[$event] = $this->getEvent($dataAction);

                continue;
            }
        }

        $part = [
            'options' => $options,
            'template' => $template,
            'element' => $widgetClass::getClassName() . '_' . $element,
            'dataAction' => $dataActionArr
                ? Json::encode(array_intersect_key($dataActionArr, array_flip(['class', 'params'])))
                : null
        ];

        if (!empty($options['unshift'])) {
            $this->parts = [$partName => $part] + $this->parts;
        } else {
            $this->parts[$partName] = $part;
        }

        return $this;
    }

    //    /**
//     * @param array $params
//     * @return Widget_Data
//     */
//    public function bind(array $params)
//    {
//        foreach ($params as $key => $value) {
//
//            $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
//            $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';
//
//            if (preg_match($ascPattern, $value)) {
//                $value = Query_Builder::SQL_ORDERING_ASC;
//            } elseif (preg_match($descPattern, $value)) {
//                $value = Query_Builder::SQL_ORDERING_DESC;
//            } else {
//                $value = '';
//            }
//
//            if (isset($this->columns[$key])) {
//                if (empty($value) && isset($this->columns[$key]['options']['default'])) {
//                    $value = $this->columns[$key]['options']['default'];
//                }
//
//                $this->bind([$key => $value]);
//            }
//        }
//
//        return $this;
//    }

    /**
     * Compiled widget parts
     *
     * @param null $filterParts
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo: Зачечем весь этот код?
     * @version 1.0
     * @since   1.0
     */
    public function getParts($filterParts = null)
    {
        $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
        $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';

        $parts = [];

        foreach ($this->parts as $partName => $part) {
            if (!empty($filterParts) && !in_array($partName, $filterParts)) {
                continue;
            }

            $value = $this->getValue($partName);

            if (is_string($value) && !empty($value) && isset($part['options']['sort'])) {
                if (preg_match($ascPattern, $value)) {
                    $part['options']['sort'] = Query_Builder::SQL_ORDERING_ASC;
                } elseif (preg_match($descPattern, $value)) {
                    $part['options']['sort'] = Query_Builder::SQL_ORDERING_DESC;
                } else {
                    $part['options']['sort'] = 'NONE';
                }
            }

            $parts[$partName] = $part;
        }

        return $parts;
    }

    /**
     * Compiled values of widget
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   1.0
     */
    public function getValues()
    {
        $values = [];

        $filterParts = $this->getFilterParts();

        foreach ($this->values as $partName => $value) {
            if (!empty($filterParts) && !isset($filterParts[$partName])) {
                continue;
            }

            if (is_string($value)) {
                if ($param = strstr($value, '/' . Query_Builder::SQL_ORDERING_ASC, false) !== false) {
                    $value = $param;
                } elseif ($param = strstr($value, '/' . Query_Builder::SQL_ORDERING_DESC, false) !== false) {
                    $value = $param;
                }
            }

            $values[$partName] = $value;
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getFilterParts()
    {
        return $this->filterParts;
    }

    public function getPart($partName)
    {
        return isset($this->parts[$partName]) ? $this->parts[$partName] : null;
    }

    /**
     * @param null $key
     * @return array
     */
    public function getData($key = null)
    {
        if (!$key) {
            return $this->data;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $dataAction
     * @return string
     */
    protected function getEvent(array $dataAction)
    {
        return $dataAction
            ? 'Ice_Core_Widget.click($(this)' .
            ($dataAction['callback'] ? ', \'' . $dataAction['callback'] . '\'' : '') .
            '); return false;'
            : '';
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function a($columnName, array $options = [], $template = 'Ice\Widget\A')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build column part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return Widget $this_Data
     */
    public function span($columnName, $options = [], $template = 'Ice\Widget\Span')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build div part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function div($columnName, array $options = [], $template = 'Ice\Widget\Div')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build p part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function p($columnName, array $options = [], $template = 'Ice\Widget\P')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build p part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function text($columnName, array $options = [], $template = 'Ice\Widget\Text')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build img part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function img($columnName, array $options = [], $template = 'Ice\Widget\Img')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return $this
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     */
    public function button($fieldName, array $options = [], $template = 'Ice\Widget\Button')
    {
        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
    }

    /**
     * @param $scope
     * @param array $data
     * @param Widget $widgetClass
     * @return $this
     */
    public function scope($scope, array $data = [], $widgetClass = null)
    {
        /** @var Widget $widgetClass */
        $widgetClass = $widgetClass
            ? Widget::getClass($widgetClass)
            : get_class($this);

        return Widget_Scope::getInstance($widgetClass)->$scope($this, $data);
    }

    /**
     * @param string $redirect
     * @param int $timeout
     * @return $this Widget_Form_Security
     */
    public function setRedirect($redirect, $timeout = 0)
    {
        $this->redirect = $redirect;
        $this->setTimeout($timeout);

        return $this;
    }

    /**
     * @param int $timeout
     * @return Widget_Form_Security
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return null
     */
    public function getParentWidgetId()
    {
        return $this->parentWidgetId;
    }

    /**
     * @param null $parentWidgetId
     */
    public function setParentWidgetId($parentWidgetId)
    {
        $this->parentWidgetId = $parentWidgetId;
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return $this
     */
    protected function widget($name, array $options = [], $template = 'Ice\Widget\Widget')
    {
        if (empty($options['postfix'])) {
            $options['postfix'] = null;
        }

        if (empty($options['params'])) {
            $options['params'] = [];
        }

        $options['params']['parentWidgetId'] = $this->getId();

        $options['widget'] = $this->getWidget($options['widget'], $options['postfix'], $options['params']);

        if (!$options['widget']->getResource()) {
            $options['widget']->setResource($this->getResource());
        }

        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $widgetClass
     * @return Widget
     */
    protected function getWidgetClass($widgetClass)
    {
        return $widgetClass[0] == '_'
            ? get_class($this) . $widgetClass
            : Widget::getClass($widgetClass);
    }


    public function getId()
    {
        return 'Widget_' . Object::getClassName(get_class($this)) . '_' . $this->getInstanceKey();
    }

    protected static function getDefaultKey()
    {
        return Router::getInstance()->getName();
    }

    /**
     * @param string $widgetClass
     * @param null $postfixKey
     * @param array $params
     * @return Widget
     */
    protected function getWidget($widgetClass, $postfixKey = null, array $params = [])
    {
        if (is_object($widgetClass)) {
            return $widgetClass;
        }

        $widgetClass = $this->getWidgetClass($widgetClass);

        $key = strtolower(Object::getClassName(get_class($this))) . (empty($postfixKey) ? '' : '_' . $postfixKey);

        return $widgetClass::getInstance($key, null, $params);
    }

    /**
     * @param array|null $filterParts
     * @return Widget[]
     */
    protected function getWidgets($filterParts = null)
    {
        $widgets = [];

        foreach ($this->getParts($filterParts) as $partName => $part) {
            if (isset($part['options']['widget'])) {
                $widgets[$partName] = $part['options']['widget'];
            }
        }

        return $widgets;
    }

    protected function getDataAction()
    {
        if ($this->dataAction !== null) {
            return $this->dataAction;
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $this->setDataAction($widgetClass::getConfig()->gets('action'));
    }

    /**
     * @param array $dataAction
     * @return array
     */
    public function setDataAction(array $dataAction)
    {
        if ($this->dataAction !== null || empty($dataAction)) {
            return $this->dataAction;
        }

        if (isset($dataAction['url'])) {
            $dataAction['url'] = $dataAction['url'] === true
                ? Request::uri(true)
                : Router::getInstance()->getUrl($dataAction['url']);
        }

        $this->dataAction = $dataAction;

        return $this->dataAction;
    }

    private function loadResource()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        if ($widgetClass == Resource_Dynamic::getClass()) {
            return;
        }

        if ($widgetClass::getConfig()->get('resource/js') === true) {
            $this->getResourceDynamic()->addResource($widgetClass, 'js');
        }

        if ($widgetClass::getConfig()->get('resource/css') === true) {
            $this->getResourceDynamic()->addResource($widgetClass, 'css');
        }

        if ($widgetClass::getConfig()->get('resource/less') === true) {
            $this->getResourceDynamic()->addResource($widgetClass, 'less');
        }
    }

    /**
     * @return Resource_Dynamic
     */
    private function getResourceDynamic() {
        return Resource_Dynamic::getInstance(null);
    }
}
