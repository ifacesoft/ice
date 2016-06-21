<?php
namespace Ice\Core;

use Ice\Exception\Access_Denied;
use Ice\Exception\Http;
use Ice\Exception\RouteNotFound;
use Ice\Helper\Access;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\Helper\Transliterator;
use Ice\Widget\Bootstrap3_Table_Row;
use Ice\Widget\Resource_Dynamic;
use Ice\WidgetComponent\FormElement;
use Ice\WidgetComponent\HtmlTag;
use Ice\WidgetComponent\HtmlTag_A;
use Ice\WidgetComponent\Widget as WidgetComponent_Widget;


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

    private $output = null;

    private $token = null;
    private $data = [];

    private $result = null;
    private $compiledResult = null;

    private $options = [];

    private $parentWidgetId = null;
    private $parentWidgetClass = null;
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

    private $resourceClass = null;
    private $templateClass = null;
    private $renderClass = null;

    private $layout = null;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => 'Ice\Widget\Blank', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOption($name = null, $default = null)
    {
        if ($name === null) {
            return $this->options;
        }

        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @return array
     */
    protected function getRows()
    {
        return $this->rows;
    }

    public function getCanonicalName($part = 'header')
    {
        return String::truncate(Transliterator::transliterate(strip_tags($this->getPart($part)->render())), 250);
    }

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

    protected function __construct(array $data)
    {
        parent::__construct($data);

        unset($data['instanceKey']);

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $key = 'build - ' . get_class($this) . '/' . $this->getInstanceKey();

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        try {
            $this->token = crc32(String::getRandomString());

            $this->setResourceClass();
            $this->setTemplateClass();
            $this->setRenderClass();

            if (isset($data['parentWidgetId'])) {
                $this->parentWidgetId = $data['parentWidgetId'];
                unset($data['parentWidgetId']);
            }

            if (isset($data['parentWidgetClass'])) {
                $this->parentWidgetClass = $data['parentWidgetClass'];
                unset($data['parentWidgetClass']);
            }

            $configInput = $widgetClass::getConfig()->gets('input', []);
            $configOutput = $widgetClass::getConfig()->gets('output', []);

            $this->setData($data);

            $this->bind(Input::get($configInput, $data));

            $this->loadResource();

            $this->output = array_merge(Input::get($configOutput, $data), (array)$this->build($this->getValues()));
        } catch (Http $e) {
            throw $e;
//        } catch (Access_Denied $e) {
//            throw $e;
        } catch (\Exception $e) {
            Logger::getInstance(__CLASS__)->error(['Widget {$0} init failed', $widgetClass], __FILE__, __LINE__, $e);
        } finally {
            Profiler::setPoint($key, $startTime, $startMemory);

            Logger::fb(Profiler::getReport($key), 'widget', 'INFO');
        }
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        if (!$this->resourceClass) {
            return null;
        }

        return Resource::create($this->resourceClass);
    }

    /**
     * Init resource class
     *
     * @param Resource|string|null $resourceClass
     * @return $this
     */
    public function setResourceClass($resourceClass = null)
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $this->resourceClass = $resourceClass !== null
            ? $resourceClass
            : $widgetClass::getConfig()->get('render/resource', null);

        if ($this->resourceClass instanceof Resource) {
            $this->resourceClass = $this->resourceClass->getResourceClass();
        }

        if ($this->resourceClass === true) {
            $this->resourceClass = $widgetClass::getClass();
        }

        if ($this->resourceClass === false) {
            $this->resourceClass = null;
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    /**
     * @param string $templateClass
     * @return null|string
     */
    public function setTemplateClass($templateClass = null)
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $this->templateClass = $templateClass
            ? $templateClass
            : $widgetClass::getConfig()->get('render/template', null);

        if (empty($this->templateClass)) {
            $this->templateClass = $this->templateClass === '' ? 'Ice\Widget\Blank' : 'Ice\Widget\Default';
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        if ($this->templateClass === true) {
            $this->templateClass = $widgetClass;
        }

        if ($this->templateClass[0] == '_') {
            $this->templateClass = $widgetClass . $this->templateClass;
        }

        return $this;
    }

    /**
     * @return Render
     */
    public function getRender()
    {
        /** @var Render $renderClass */
        $renderClass = Render::getClass($this->renderClass);

        return $renderClass::getInstance();
    }

    /**
     * @param $renderClass
     * @return Render
     */
    public function setRenderClass($renderClass = null)
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $this->renderClass = $renderClass
            ? $renderClass
            : $widgetClass::getConfig()->get('render/class', null);

        if (empty($this->renderClass) || $this->renderClass === true) {
            $this->renderClass = Config::getInstance(Render::getClass())->get('default');
        }

        return $this;
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
//        /** @var Widget $widgetClass */
//        $widgetClass = self::getClass();
//        try {
//            Access::check($widgetClass::getConfig()->gets('access'));
//        } catch (Access_Denied $e) {
//            //
//            return null;
//        }

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
     * @param string $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        $this->classes .= ' ' . $classes;
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
        $values = array_merge($this->values, $params);
        
        foreach ($this->getParts() as $partName => $part) {
            if (array_key_exists($partName, $values)) {
                $part->set($partName, $values[$partName]);
                unset($values[$partName]);
            }
        }
        
        $this->values = $values;
        
        return $this;
    }

    public function setQueryResult(QueryResult $queryResult)
    {
        foreach ($this->getParts() as $part) {
            if ($part instanceof WidgetComponent_Widget) {
                $part->getWidget()->setQueryResult($queryResult);
            }
        }
    }

    public function queryBuilderPart(QueryBuilder $queryBuilder, array $input)
    {
        foreach ($this->getParts() as $part) {
            if ($part instanceof WidgetComponent_Widget) {
                $part->getWidget()->queryBuilderPart($queryBuilder, $input);
            }
        }
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return Logger::getInstance(__CLASS__)
                ->error(['Render widget {$0} was failed', [get_class($this)]], __FILE__, __LINE__, $e);
        }
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getResult()
    {
        $this->result = [];

        $offset = $this->getOffset();
        $values = $this->getValues();

        foreach ($this->getRows() as $row) {
            $offset++;

            $rowTable = [];

            foreach ($this->getParts($this->getFilterParts()) as $partName => $part) {
                $rowTable[$partName] = $part->cloneComponent();// todo: избавиться от клонирования (дублирования билдинга)

                if (!($this instanceof Bootstrap3_Table_Row)) {
                    $rowTable[$partName]->build(array_merge($values, $row));
                }

                $rowTable[$partName]->setOffset($offset);
            }

            $this->result[$offset] = $rowTable;
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

        return $this->compiledResult = array_merge(
            [
                'result' => $this->getResult(),
                'widgetId' => $this->getWidgetId(),
                'widgetClass' => $this->getWidgetClassName(),
                'parentWidgetId' => $this->getParentWidgetId(), // Widget_Admin_Access_Book_Packet_Table_admin_block
//                'parentWidgetClass' => $this->getParentWidgetClass(), // "Ebs\\Widget\\Admin_Access_Book_Packet_Table
                'widgetData' => $this->getData(),
                'widgetResource' => $this->getResource(),
                'classes' => trim($this->classes),
                'dataParams' => Json::encode($this->getDataParams()),
                'dataWidget' => Json::encode($this->getDataWidget())
            ],
            (array)$this->output
        );
    }

    private function getWidgetClassName()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $widgetClass::getClassName();
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

    public function render(Render $render = null)
    {
        if ($render === null) {
            $render = $this->getRender();
        }

        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $result = $render
            ->fetch(
                $this->getTemplateClass(),
                array_merge($this->getCompiledResult(), ['widget' => $this, 'render' => $render]),
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

        $dataParams = $this->getValues();

        foreach ($this->getParts() as $part) {
            $dataParams = array_merge($part->getParams(), $dataParams);
        }

        return $dataParams;
    }

    /**
     * @param array $dataParams
     */
    public function setDataParams(array $dataParams)
    {
        $this->dataParams = array_merge($this->getDataParams(), $dataParams);

        foreach ($this->getParts() as $part) {
            if ($part instanceof WidgetComponent_Widget) {
                $part->getWidget()->setDataParams($this->dataParams);
            }
        }
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

    /**
     * @param $key
     * @deprecated Use Widget::get
     * @return mixed|null
     */
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
     * @param WidgetComponent $part
     * @return $this
     */
    protected function addPart(WidgetComponent $part)
    {
        if (!$part->getOption('isShow', true)) {
            return $this;
        }

        try {
            $roles = $part->getOption('roles');
            $access = $part->getOption('access');

            if (!$access) {
                $access = [];
            }

            if ($roles) {
                $access['roles'] = $roles;
            }

            Access::check($access);
        } catch (Access_Denied $e) {
            return $this;
        }

        $componentName = $part->getComponentName();

        if (!empty($options['rewrite']) && isset($this->parts[$componentName])) {
            unset($this->parts[$componentName]);
        }

        if (!empty($options['unshift'])) {
            $this->parts = [$componentName => $part] + $this->parts;
        } else {
            $this->parts[$componentName] = $part;
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
//            $ascPattern = '/(?:[^\/]+\/)?' . QueryBuilder::SQL_ORDERING_ASC . '$/';
//            $descPattern = '/(?:[^\/]+\/)?' . QueryBuilder::SQL_ORDERING_DESC . '$/';
//
//            if (preg_match($ascPattern, $value)) {
//                $value = QueryBuilder::SQL_ORDERING_ASC;
//            } elseif (preg_match($descPattern, $value)) {
//                $value = QueryBuilder::SQL_ORDERING_DESC;
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
     * @return WidgetComponent[]
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo: Зачечем весь этот код?
     * @version 1.0
     * @since   1.0
     */
    public function getParts($filterParts = null)
    {
        $ascPattern = '/(?:[^\/]+\/)?' . QueryBuilder::SQL_ORDERING_ASC . '$/';
        $descPattern = '/(?:[^\/]+\/)?' . QueryBuilder::SQL_ORDERING_DESC . '$/';

        $parts = [];

        foreach ($this->parts as $partName => $part) {
//            if (!empty($filterParts) && !in_array($partName, $filterParts)) {
//                continue;
//            }

            // Todo: этот код перенести в компонент -> Очень важно!!!
//            $value = $this->getValue($partName);
//
//            if (is_string($value) && !empty($value) && isset($part['options']['sort'])) {
//                if (preg_match($ascPattern, $value)) {
//                    $part['options']['sort'] = QueryBuilder::SQL_ORDERING_ASC;
//                } elseif (preg_match($descPattern, $value)) {
//                    $part['options']['sort'] = QueryBuilder::SQL_ORDERING_DESC;
//                } else {
//                    $part['options']['sort'] = 'NONE';
//                }
//            }
//
            $parts[$partName] = /*$row ? $part->build($row, $this) : */
                $part;
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
     * @version 1.1
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
                if ($param = strstr($value, '/' . QueryBuilder::SQL_ORDERING_ASC, false) !== false) {
                    $value = $param;
                } elseif ($param = strstr($value, '/' . QueryBuilder::SQL_ORDERING_DESC, false) !== false) {
                    $value = $param;
                }

//                $value = htmlentities($value, ENT_QUOTES);
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

    public function setPart($partName, $part)
    {
        $this->parts[$partName] = $part;
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
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function a($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new HtmlTag_A($columnName, $options, $template, $this));
    }

    /**
     * Build column part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this $this_Data
     */
    public function span($columnName, array $options = [], $template = 'Ice\Widget\Span')
    {
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
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
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
    }

    /**
     * Build div part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function error($columnName, array $options = [], $template = 'Ice\WidgetComponent\Bootstrap\Alert\Danger')
    {
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
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
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
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
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
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
        return $this->addPart(new HtmlTag($columnName, $options, $template, $this));
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
     * @param string $route
     * @param int $timeout
     * @return $this
     */
    public function setRedirect($route, $timeout = 0)
    {
        if (is_array($route)) {
            list($route, $params) = $route;
        } else {
            $params = [];
        }

        try {
            $this->redirect = $route === true
                ? Router::getInstance()->getUrl([null, $params])
                : Router::getInstance()->getUrl([$route, $params]);
        } catch (RouteNotFound $e) {
            $this->redirect = $route;
        }

        $this->setTimeout($timeout);

        return $this;
    }

    /**
     * @param int $timeout
     * @return $this
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
     * @return null
     */
    public function getParentWidgetClass()
    {
        return $this->parentWidgetClass;
    }

    /**
     * @param null $parentWidgetClass
     */
    public function setParentWidgetClass($parentWidgetClass)
    {
        $this->parentWidgetClass = $parentWidgetClass;
    }

    public function cloneWidget()
    {
        return clone $this;
    }

    /**
     * @param string $columnName
     * @param array $options
     * @param string $template
     * @return $this
     */
    public function widget($columnName, array $options = [], $template = null)
    {
        try {
            Access::check($options['widget']::getConfig()->gets('access'));
        } catch (Access_Denied $e) {
            return $this;
        }

        return $this->addPart(new WidgetComponent_Widget($columnName, $options, $template, $this));
    }

    public function getWidgetId()
    {
        return Object::getClassName(get_class($this)) . '_' . strtolower(str_replace('\\', '_', $this->getInstanceKey()));
    }

    protected static function getDefaultKey()
    {
        return Router::getInstance()->getName();
    }

    /**
     * @param string $widgetClass
     * @param string $postfixKey
     * @return $this
     */
    public function getWidget($widgetClass, $postfixKey = '')
    {
        if ($widgetClass instanceof Widget) {
            $widgetClass->setParentWidgetId($this->getInstanceKey());
            $widgetClass->setParentWidgetClass(get_class($this));

            return $widgetClass;
        }

        $widgetClass = (array)$widgetClass;

        if (count($widgetClass) == 3) {
            list($widgetClass, $widgetParams, $instanceKey) = $widgetClass;
        } else if (count($widgetClass) == 2) {
            list($widgetClass, $widgetParams) = $widgetClass;
            $instanceKey = null;
        } else {
            $widgetClass = reset($widgetClass);
            $widgetParams = [];
            $instanceKey = null;
        }

        $key = null;

        if (!$instanceKey || $instanceKey[0] == '_') {
            $key = strtolower(Object::getClassName(get_class($this)));

            if ($instanceKey[0] == '_') {
                $key .= $instanceKey;
            }
        } else {
            $key = $instanceKey;
        }

        /** @var Widget $widgetClass */
        $widgetClass = $widgetClass[0] == '_'
            ? get_class($this) . $widgetClass
            : Widget::getClass($widgetClass);

        /** @var Widget $widget */
        $widget = null;

        $widgetParams['parentWidgetId'] = $this->getInstanceKey();
        $widgetParams['parentWidgetClass'] = get_class($this);

//        try {
        $widget = $widgetClass::getInstance($key . $postfixKey, null, $widgetParams);
//        } catch (\Exception $e) {
//            //todo: заменять на виджет сообщения об ошибке
//        }

        if (!$widget->getResource()) {
            $widget->setResourceClass($this->getResource());
        }

        return $widget;
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
    private function getResourceDynamic()
    {
        return Resource_Dynamic::getInstance(null);
    }

    /**
     * @param $name
     * @return $this
     */
    public function removePart($name)
    {
        unset($this->parts[$name]);
        return $this;
    }

    /**
     * @param $token
     * @return bool
     *
     * @todo: need implement
     */
    public function checkToken($token)
    {
//        throw new Error('token expired');
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function getRenderEvent()
    {
        return [
            'type' => 'onclick',
            'action' => Render::class,
            'params' => ['widgets' => [$this->getParentWidgetId() => $this->getParentWidgetClass()]],
            'ajax' => true,
            'callback' => null,
            'confirm_massage' => null
        ];
    }

    public function getRenderRoute()
    {
        $router = Router::getInstance();

        return ['name' => $router->getName(), 'params' => $router->getParams()];
    }

    /**
     * @param $param string|null
     * @param array $options
     * @return mixed
     */
    public function get($param = null, $options = [])
    {
        if ($param === null) {
            return $this->values;
        }

        $value = array_key_exists($param, $this->values) ? $this->values[$param] : null;

        if ($value === null && array_key_exists('default', $options)) {
            $value = $options['default'];
        }

        return $value;
    }

    public function validate()
    {
        $values = [];

        foreach ($this->getParts($this->getFilterParts()) as $component) {
            $name = $component instanceof FormElement ? $component->getName() : $component->getComponentName();
            $values[$name] = $component->validate();
        }

        return $values;
    }
}
