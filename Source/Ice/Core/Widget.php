<?php
namespace Ice\Core;

use Composer\Installer\PackageEvent;
use Ice\DataProvider\Request as DataProvider_Request;
use Ice\Exception\Access_Denied;
use Ice\Exception\Http;
use Ice\Exception\RouteNotFound;
use Ice\Helper\Access;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\Helper\Transliterator;
use Ice\Widget\Resource_Dynamic;
use Ice\WidgetComponent\HtmlTag;
use Ice\WidgetComponent\HtmlTag_A;
use Ice\WidgetComponent\Widget as WidgetComponent_Widget;
use Ice\WidgetComponent\Alert as WidgetComponent_Alert;

abstract class Widget extends Container
{
    use Stored;
    use Configured;

    /**
     * Widget parts (fields for Form, columns for Data, items for Menu)
     *
     * @var array
     */
    protected $parts = [];
//    private $values = [];
    /**
     * @var array rows of values
     */
    private $rows = [[]];
    /**
     * @var int
     */
    private $offset = 0;
    private $classes = '';

//    private $dataParams = null;

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

    protected function __construct(array $defaultData)
    {
        parent::__construct($defaultData);

        unset($defaultData['instanceKey']);

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

            if (isset($defaultData['parentWidgetId'])) {
                $this->parentWidgetId = $defaultData['parentWidgetId'];
                unset($defaultData['parentWidgetId']);
            }

            if (isset($defaultData['parentWidgetClass'])) {
                $this->parentWidgetClass = $defaultData['parentWidgetClass'];
                unset($defaultData['parentWidgetClass']);
            }

            Access::check($widgetClass::getConfig()->gets('access', ['env' => null, 'request' => null, 'roles' => null, 'message' => '']));

            $this->init($defaultData);

            $this->loadResource();

            $this->output = array_merge(
                Input::get($widgetClass::getConfig()->gets('output', []), $defaultData),
                (array)$this->build($this->get())
            );
        } catch (Http $e) {
            throw $e;
        } catch (Access_Denied $e) {
            $this->setTemplateClass('Ice\Widget\Blank');

            $message = $e->getMessage();

            if ($message) {
                $this->alert('access_denied', ['classes' => 'alert-danger', 'params' => ['access_denied' => $e->getMessage()]]);
            }
        } catch (\Exception $e) {
            Logger::getInstance(__CLASS__)->error(['Widget {$0} init failed', $widgetClass], __FILE__, __LINE__, $e);
        } finally {
            Profiler::setPoint($key, $startTime, $startMemory);

            Logger::fb(Profiler::getReport($key), 'widget', 'INFO');
        }
    }

    protected function init($data)
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $this->set(Input::get($widgetClass::getConfig()->gets('input', []), $data));
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
     * @param $renderClass
     * @return Widget
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
     * @param array $params
     * @return $this
     */
    public function set(array $params)
    {
        $this->getWidgetRegistry()->set($params);

        return $this;
    }

    public function getWidgetRegistry()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $widgetClass::getRegistry($this->getWidgetId());
    }

    public function getWidgetId()
    {
        return Object::getClassName(get_class($this)) . '_' . strtolower(str_replace('\\', '_', $this->getInstanceKey()));
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
     * @param string $instanceKey
     * @param null $ttl
     * @param array $params
     * @return Widget|Container|$this
     */
    public static function getInstance($instanceKey, $ttl = null, array $params = [])
    {
//        /** @var Widget $widgetClass */
//        $widgetClass = self::getClass();
//        try {
//            Access::check($widgetClass::getConfig()->gets('access'));
//        } catch (Access_Denied $e) {
//            //
//            return null;
//        }

        return parent::getInstance($instanceKey, $ttl, $params);
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
     *          'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
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
     * @param $paramName string|null
     * @param null $default
     * @return mixed
     */
    public function get($paramName = null, $default = null)
    {
        $params = $this->getWidgetRegistry()->get();

        foreach ($this->getParts() as $part) {
            $params = array_merge($part->get(), $params);
        }

        if ($paramName === null) {
            return empty($params) ? [] : $params;
        }

        return array_key_exists($paramName, $params) ? $params[$paramName] : $default;
    }

    /**
     * Compiled widget parts
     *
     * @param null $filterParts
     *
     * @return WidgetComponent[]
     *
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
//            $value = $this->get($partName);
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
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => 'Ice\Widget\Blank', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    protected static function getDefaultKey()
    {
        return Router::getInstance()->getName();
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

    public function getCanonicalName($part = 'header')
    {
        return String::truncate(Transliterator::transliterate(strip_tags($this->getPart($part)->render())), 250);
    }

    /**
     * @param $partName
     * @return WidgetComponent|WidgetComponent_Widget
     */
    public function getPart($partName)
    {
        return isset($this->parts[$partName]) ? $this->parts[$partName] : null;
    }

    /**
     * @param string $classes
     *
     * @param bool $replace
     * @return $this
     */
    public function addClasses($classes, $replace = false)
    {
        if ($replace) {
            $this->classes = $classes;
        } else {
            $this->classes .= ' ' . $classes;
        }

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

    /**
     * @param QueryBuilder $queryBuilder
     * @deprecated 1.1 Use $queryBuilder->filter($formWidget). Filter by all components of widget
     */
    public function queryBuilderPart(QueryBuilder $queryBuilder)
    {
        foreach ($this->getParts() as $part) {
            if ($part instanceof WidgetComponent_Widget) {
                $part->getWidget()->queryBuilderPart($queryBuilder);
            }
        }
    }

    /**
     *
     * @deprecated call directly render()
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return Logger::getInstance(__CLASS__)
                ->error(['Render widget {$0} was failed', [get_class($this)]], __FILE__, __LINE__, $e);
        }
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
     * @return string|null
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    /**
     * @param string $templateClass
     *
     * @todo: Написать обработчик (init) конфига, где будет отдельный вызов setTemplateClass
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
                'dataParams' => Json::encode($this->get()),
                'dataWidget' => Json::encode($this->getDataWidget())
            ],
            (array)$this->output
        );
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getResult()
    {
        $this->result = [];

        $offset = $this->getOffset();

        foreach ($this->getRows() as $row) {
            $offset++;

            $rowTable = [];

            unset($part);

            $parts = $this->getParts($this->getFilterParts());

            /**
             * @var string $partName
             * @var WidgetComponent $part
             */
            foreach ($parts as $partName => $part) {
                $part = $part->cloneComponent($offset);

                $part->set($row);

                $rowTable[$partName] = $part;
            }

            $this->result[$offset] = $rowTable;
        }

        return $this->result;
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
     * @return array
     */
    protected function getRows()
    {
        return $this->rows;
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
     * @return array
     */
    public function getFilterParts()
    {
        return $this->filterParts;
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

    private function getWidgetClassName()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $widgetClass::getClassName();
    }

//    /**
//     * Compiled values of widget
//     *
//     * @return array
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 1.1
//     * @since   1.0
//     */
//    public function getValues()
//    {
//        $values = [];
//
//        $filterParts = $this->getFilterParts();
//
//        foreach ($this->values as $partName => $value) {
//            if (!empty($filterParts) && !isset($filterParts[$partName])) {
//                continue;
//            }
//
//            if (is_string($value)) {
//                if ($param = strstr($value, '/' . QueryBuilder::SQL_ORDERING_ASC, false) !== false) {
//                    $value = $param;
//                } elseif ($param = strstr($value, '/' . QueryBuilder::SQL_ORDERING_DESC, false) !== false) {
//                    $value = $param;
//                }
//
////                $value = htmlentities($value, ENT_QUOTES);
//            }
//
//            $values[$partName] = $value;
//        }
//
//        return $values;
//    }

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
     * @return Resource
     */
    public function getResource()
    {
        if (!$this->resourceClass) {
            return null;
        }

        return Resource::create($this->resourceClass);
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

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function walkOptions($function, $option)
    {
        array_walk($this->parts, $function, $option);
        return $this;
    }

    public function setPart($partName, $part)
    {
        $this->parts[$partName] = $part;
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
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function alert($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new WidgetComponent_Alert($columnName, $options, $template, $this));
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

        $access = $part->getOption('access', ['roles' => []]);

        if ($access['roles'] && !Security::getInstance()->check((array)$access['roles'])) {
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
     * @return null|string
     */
    public function getRedirect()
    {
        return $this->redirect;
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
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
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

    public function getRenderRoute()
    {
        $router = Router::getInstance();

        return ['name' => $router->getName(), 'params' => $router->getParams()];
    }

    /**
     * @param $paramName string|null
     * @param null $default
     * @return mixed
     */
    public function getAll($paramName = null, $default = null)
    {
        $params = $this->getWidgetRegistry()->get();

        foreach ($this->getParts() as $part) {
            $params = array_merge($part->getAll(), $params);
        }

        if ($paramName === null) {
            return empty($params) ? [] : $params;
        }

        return array_key_exists($paramName, $params) ? $params[$paramName] : $default;
    }

    public function validate()
    {
        $params = Validator::validateParams($this->getWidgetRegistry()->get(), $this->getInputConfig());

        foreach ($this->getParts($this->getFilterParts()) as $component) {
            $params = array_merge($params, $component->validate());
        }

        return $params;
    }

    public function getInputConfig()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return $widgetClass::getConfig()->gets('input', []);
    }

    public function getError($message)
    {
        return $this->getLogger()->info($message, Logger::DANGER, $this->getResource());
    }

    public function getSuccess($message)
    {
        return $this->getLogger()->info($message, Logger::SUCCESS, $this->getResource());
    }
}
