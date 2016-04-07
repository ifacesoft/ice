<?php
namespace Ice\Core;

use DateTime;
use DateTimeZone;
use Ice\Exception\Access_Denied;
use Ice\Exception\Error;
use Ice\Exception\Http;
use Ice\Exception\RouteNotFound;
use Ice\Helper\Access;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\Render\Php;
use Ice\Render\Replace;
use Ice\Widget\Resource_Dynamic;

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

    private $template = null;

    private $layout = null;

    private $indexOffset = 0;

    /**
     * @param $name
     * @return array
     */
    public function getOption($name)
    {
        return $this->options[$name];
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
        /** @var Widget $class */
        $class = get_called_class();

        $repository = $class::getRepository('resource');

        if ($resource instanceof Resource) {
            return $repository->set($this->getInstanceKey(), $resource);
        }

        if ($resource === null && !$force) {
            $resource = $class::getConfig()->get('render/resource');
        }

        if ($resource === null) {
            return null;
        }

        if ($resource === false) {
            return $repository->set($this->getInstanceKey(), false);
        }

        if ($resource === true || (is_array($resource) && !isset($resource['class']))) {

            $resource = $class;
        }

        if (is_array($resource)) {
            $resource = $resource['class'];
        }

        return $repository->set($this->getInstanceKey(), Resource::create($resource));
    }

    /**
     * @param null $resource
     * @param bool|false $force
     * @return Resource
     */
    public function getResource($resource = null, $force = false)
    {
        if ($resource) {
            return Resource::create($resource);
        }

        /** @var Widget $class */
        $class = get_called_class();

        $repository = $class::getRepository('resource');

        if (!$force && $repository->get($this->getInstanceKey()) !== null) {
            return $repository->get($this->getInstanceKey());
        }

        return $this->setResource($resource);
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

            if (isset($data['parentWidgetId'])) {
                $this->parentWidgetId = $data['parentWidgetId'];
                unset($data['parentWidgetId']);
            }

            if (isset($data['parentWidgetClass'])) {
                $this->parentWidgetClass = $data['parentWidgetClass'];
                unset($data['parentWidgetClass']);
            }

            $configInput = $widgetClass::getConfig()->gets('input', false);
            $configOutput = $widgetClass::getConfig()->gets('output', false);

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
//
//        Access::check($widgetClass::getConfig()->gets('access'));

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
        $this->values = array_merge($this->values, $params);
        return $this;
    }

    public function setQueryResult(QueryResult $queryResult)
    {
        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->setQueryResult($queryResult);
            }
        }
    }

    public function queryBuilderPart(QueryBuilder $queryBuilder, array $input)
    {
        foreach ($this->getParts() as $partName => $part) {
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
            return Logger::getInstance(__CLASS__)
                ->error(['Render widget {$0} was failed', [get_class($this)]], __FILE__, __LINE__, $e);
        }
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function getResult(array $data = [])
    {
        if ($this->result !== null) {
            return $this->result;
        }

        $this->result = [];

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $offset = $this->getOffset();
        $index = isset($data['index']) ? $data['index'] : 0;

        $values = $this->getValues();

        foreach ($this->getRows() as $row) {
            $column = isset($data['column']) ? $data['column'] : 'A';

            $isRow = !empty($row);

            $row = array_merge($values, $row);

            $empty = empty($row);

            if (empty($row)) {
                $part['empty'] = true;
            }

            $rowTable = [];

            foreach ($this->getParts($this->getFilterParts()) as $partName => $part) {
                $part['empty'] = $empty;

                if (isset($data['sheet'])) {
                    $part['sheet'] = $data['sheet'];
                }

                $resourceParams = [];

                $part['resource'] = $this->getResource();
//
//                if ($partName == 'btnExcel') {
//                    Debuger::dump([$part['options'], $part['resource']]);die();
//                }

                if (array_key_exists('resource', $part['options'])) {
                    if (empty($part['options']['resource'])) {
                        $part['resource'] = null;
                    } else {
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
                    }
                }

                $this->partParams($partName, $part, $row);

                if (!empty($part['options']['route'])) {
                    if ($part['options']['route'] === true) {
                        $part['options']['route'] = $partName;
                    }

                    $part['options']['route'] = (array)$part['options']['route'];
                    $tempRouteParams = [];
                    $withGet = false;
                    $withDomain = false;

                    if (count($part['options']['route']) == 4) {
                        list($routeName, $tempRouteParams, $withGet, $withDomain) = $part['options']['route'];
                    } elseif (count($part['options']['route']) == 3) {
                        list($routeName, $tempRouteParams, $withGet) = $part['options']['route'];
                    } elseif (count($part['options']['route']) == 2) {
                        list($routeName, $tempRouteParams) = $part['options']['route'];
                    } else {
                        $routeName = reset($part['options']['route']);
                    }

                    $routeParams = [];//$part['params'];

                    foreach ((array)$tempRouteParams as $routeParamKey => $routeParamValue) {
                        if (is_int($routeParamKey)) {
                            $routeParams[$routeParamValue] = !is_array($routeParamValue) && array_key_exists($routeParamValue, $row)
                                ? $row[$routeParamValue]
                                : $routeParamValue;

                            continue;
                        }

                        $routeParams[$routeParamKey] = !is_array($routeParamValue) && array_key_exists($routeParamValue, $row)
                            ? $row[$routeParamValue]
                            : $routeParamValue;
                    }

                    if (isset($part['options']['render'])) {
                        if ($part['options']['render'] === true) {
                            $part['options']['render'] = isset($part['params'][$part['value']]);
                        } else {
                            $part['options']['render'] = isset($part['params'][$part['options']['render']]);
                        }
                    } else {
                        $part['options']['render'] = true;
                    }

                    if (!$part['options']['render']) {
                        continue;
                    }

                    if (!array_key_exists('href', $part['options'])) {
                        try {
                            $part['options']['href'] = Router::getInstance()->getUrl([$routeName, $routeParams, $withGet, $withDomain]);
                        } catch (\Exception $e) {
                            throw new Error(
                                [
                                    'Url generation was failed for route {$0} in widget {$1}  (part: {$2})',
                                    [$routeName, $widgetClass::getClassName(), $partName]
                                ],
                                [$routeName, $routeParams, $withGet, $withDomain]
                            );
                        }
                    }

                    if (!array_key_exists('active', $part['options'])) {
                        $part['options']['active'] = String::startsWith(Request::uri(), $part['options']['href']);
                    }

                    if (!array_key_exists('label', $part['options']) && $routeName == $partName) {
                        $part['label'] = Resource::create(Route::getClass())->get($routeName, $routeParams);

                        $part['label'] = $part['label'] && $part['resource']
                            ? $part['resource']->get($part['label'], $resourceParams)
                            : $part['label'];
                    }
                } else {
                    if ($part['resource'] && array_key_exists('placeholder', $part['options'])) {
                        $part['options']['placeholder'] = $part['resource']->get($part['options']['placeholder']);
                    }
                }

                if (!isset($part['label'])) {
                    if (isset($part['options']['template'])) {
                        if ($part['options']['template'] === true) {
                            $part['options']['template'] = $partName;
                        }

                        if ($part['resource']) {
                            $part['options']['template'] = $part['resource']->get($part['options']['template'], $resourceParams);
                        }

                        if ($render = strstr($part['options']['template'], '/', true)) {
                            $renderClass = Render::getClass($render);
                            if (Loader::load($renderClass, false)) {
                                $part['options']['template'] = substr($part['options']['template'], strlen($render) + 1);
                            } else {
                                $renderClass = Replace::getClass();
                            }
                        } else {
                            $renderClass = Replace::getClass();
                        }

                        $part['label'] =
                            $renderClass::getInstance()->fetch(
                                $part['options']['template'],
                                $part['params'],
                                null,
                                Render::TEMPLATE_TYPE_STRING
                            );

                        unset($part['options']['template']);
                    } else {
                        $part['label'] = isset($part['options']['label']) ? $part['options']['label'] : $partName;

                        unset($part['options']['label']);

                        if ($part['label'] && $part['resource']) {
                            $part['label'] = $part['resource']->get($part['label'], $resourceParams);
                        }
                    }
                }

                if ($part['template'][0] == '_') {
                    $part['template'] = $widgetClass . $part['template'];
                }

                $part['offset'] = $offset + 1;

                $part['index'] = $index + $this->indexOffset;
                $part['column'] = $column;

                if (isset($part['options']['indexOffset'])) {
                    $part['options']['indexOffset'] += $part['index'];
                    $part['index'] = $part['options']['indexOffset'];
                }

                $part['widgetOptions'] = $this->options;

                $rowTable[$partName] = $part;

                if ($isRow) {
                    $column++;
                } else {
                    $index++;
                }
            }

            if ($isRow) {
                $index++;
            }

            $this->result[++$offset] = $rowTable;
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
                'widget' => $this,
                'result' => $this->getResult(),
                'widgetId' => $this->getWidgetId(),
                'widgetClass' => $this->getWidgetClass(),
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

    private function getWidgetClass()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        return 'Widget_' . $widgetClass::getClassName();
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

        $dataParams = $this->getValues();

        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $dataParams = array_merge($part['options']['widget']->getDataParams(), $dataParams);
            }
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
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->setDataParams($this->dataParams);
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
            if (isset($options['roles'])) {
                if (!isset($options['access'])) {
                    $options['access'] = [];
                }

                $options['access']['roles'] = $options['roles'];
                unset($options['roles']);
            }

            if (!empty($options['access'])) {
                Access::check($options['access']);
            }
        } catch (Access_Denied $e) {
            return $this;
        }

        if (!empty($options['rewrite']) && isset($this->parts[$partName])) {
            unset($this->parts[$partName]);
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        if (isset($options['default']) && $this->getValue($partName) === null) {
            $this->bind([$partName => $options['default']]);
        }

        $this->partEvents($partName, $options);

        $part = [
            'partName' => $partName,
            'options' => $options,
            'template' => $template,
            'element' => $widgetClass::getClassName() . '_' . $element,
        ];

        $part['name'] = isset($part['options']['name']) ? $part['options']['name'] : $partName;
        unset($part['options']['name']);

        $part['value'] = isset($part['options']['value']) ? $part['options']['value'] : $partName;
        unset($part['options']['value']);

        $part['title'] = isset($part['options']['title']) ? $part['options']['title'] : $partName;
        unset($part['options']['title']);

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
     * @return array
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
            if (!empty($filterParts) && !in_array($partName, $filterParts)) {
                continue;
            }

            $value = $this->getValue($partName);

            if (is_string($value) && !empty($value) && isset($part['options']['sort'])) {
                if (preg_match($ascPattern, $value)) {
                    $part['options']['sort'] = QueryBuilder::SQL_ORDERING_ASC;
                } elseif (preg_match($descPattern, $value)) {
                    $part['options']['sort'] = QueryBuilder::SQL_ORDERING_DESC;
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
     * @param $url
     * @param $method
     * @param array $event
     * @return string
     */
    protected function getOnclick($url, $method, array $event)
    {
        $code = 'Ice_Core_Widget.click($(this), \'' . $url . '\', \'' . $method . '\'';

        if (isset($event['callback'])) {
            $code .= ', ' . $event['callback'];
        }

        return $code . '); return false;';
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
     * @return $this $this_Data
     */
    public function span($columnName, array $options = [], $template = 'Ice\Widget\Span')
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
     * @param string $name
     * @param array $options
     * @param string $template
     * @return $this
     */
    public function widget($name, array $options = [], $template = 'Ice\Widget\Widget')
    {
        if (!$options['widget']) {
            return $this;
        }

        $options['widget'] = $this->getWidget($options['widget']);

        try {
            Access::check($options['widget']::getConfig()->gets('access'));
        } catch (Access_Denied $e) {
            return $this;
        }

        if ($options['widget']->getResource() === null) {
            $options['widget']->setResource($this->getResource());
        }

        if (isset($options['indexOffset'])) {
            $options['widget']->indexOffset += $options['indexOffset'];
            unset($options['indexOffset']);
        }

        $this->addPart($name, $options, $template, __FUNCTION__);

        $this->setDataParams($options['widget']->getDataParams());

        return $this;
    }

    public function getWidgetId()
    {
        return 'Widget_' . Object::getClassName(get_class($this)) . '_' . strtolower(str_replace('\\', '_', $this->getInstanceKey()));
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
        if (is_object($widgetClass)) {
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

        return $widget;
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

    public function getActionAccess($class)
    {
        $access = [];

        foreach ($this->getParts() as $part) {
            if (isset($part['options']['action']) && $part['options']['action'] == $class && isset($part['options']['access'])) {
                $access = array_merge_recursive($access, $part['options']['access']);
            }
        }

        if (!isset($access['roles'])) {
            $access['roles'] = ['ROLE_ICE_GUEST', 'ROLE_ICE_USER'];
        }

        return $access;
    }

    private function partEvents($partName, array &$options)
    {

        foreach (['onclick', 'onchange', 'submit'] as $event) {

            if (array_key_exists($event, $options)) {
                if ($options[$event] === true) {
                    throw new Error(
                        ['Temporary For part {$0} with event {$1} of widget {$2} must be defined action param',
                            [$partName, $event, get_class($this)]
                        ]
                    );
                }

                if (is_string($options[$event])) {
                    $options['dataAction'] = Json::encode([
                        'class' => '',
                        'data' => []
                    ]);
                    continue;
                }

                $actionData = [];

                if (isset($options[$event]['action'])) {
                    if ($options[$event]['action'][0] == '_') {
                        /** @var Widget $widgetClass */
                        $widgetClass = get_class($this);
                        $options[$event]['action'] = $widgetClass::getModuleAlias() . ':' .
                            $widgetClass::getClassName() . $options[$event]['action'];
                    }

                    $actionData['class'] = Action::getClass($options[$event]['action']);
                    unset($options[$event]['action']);
                }

                if (isset($options[$event]['data'])) {
                    $actionData['data'] = $options[$event]['data'];
                    unset($options[$event]['data']);
                }

                if (isset($options[$event]['url'])) {
                    try {
                        $options[$event]['url'] = $options[$event]['url'] === true
                            ? Router::getInstance()->getUrl($partName)
                            : Router::getInstance()->getUrl($options[$event]['url']);
                    } catch (RouteNotFound $e) {
                        $options[$event]['url'] = '/';
                    }
                }

                $options['url'] = isset($options[$event]['url']) ? $options[$event]['url'] : '/';
                $options['method'] = isset($options[$event]['method']) ? $options[$event]['method'] : 'POST';

                $actionData['ajax'] = array_key_exists('ajax', $options[$event]) ? $options[$event]['ajax'] : true;

                $options['dataAction'] = Json::encode($actionData);

                $options[$event] = $actionData['ajax']
                    ? $this->getOnclick($options['url'], $options['method'], $options[$event])
                    : '';
            }
        }
    }

    private function partParams($partName, array &$part, array $values)
    {
//        if ($partName == 'access_type__fk') {
//            Debuger::dump($part);
//        }

        $value = isset($values[$part['value']]) ? $values[$part['value']] : 0;
        $valueFieldName = $part['value'];

        if (isset($part['options']['oneToMany']) && empty($part['options']['rows'])) {
            $part['options']['oneToMany'] = (array)$part['options']['oneToMany'];
            $modelClass = array_shift($part['options']['oneToMany']);
            $fieldName = $modelClass::getPkFieldName();

            $firstRow = [$part['value'] => 0];

            $joinModelClasses = [$modelClass];

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $modelClass::createQueryBuilder();

            $part['title'] = (array)$part['title'];

            $fields = [];

            foreach ($part['title'] as $fieldModelClass => $fieldNames) {
                if (is_int($fieldModelClass)) {
                    $fieldModelClass = $modelClass;
                }

                if (!in_array($fieldModelClass, $joinModelClasses)) {
                    $queryBuilder->inner($fieldModelClass, '*');
                    $joinModelClasses[] = $fieldModelClass;
                }

                if ($fieldModelClass != $modelClass) {
                    $queryBuilder->asc($fieldNames, $fieldModelClass);
                }

                foreach ((array)$fieldNames as $field) {
                    $firstRow[$field] = '';
                    $fields[] = $field;
                }
            }

            $part['options']['rows'] = array_merge(
                [$firstRow],
                $queryBuilder
                    ->getSelectQuery(array_merge([$fieldName => $part['value']], isset($part['title'][$modelClass]) ? (array)$part['title'][$modelClass] : (array)reset($part['title'])))
                    ->getRows()
            );

            $oneToMany = array_filter($part['options']['rows'], function ($item) use ($value, $valueFieldName) {
                return $item[$valueFieldName] == $value;
            });

            $part['title'] = $fields;

            $one = $oneToMany ? reset($oneToMany) : array_fill_keys((array)$part['title'], '');

            $part['oneToMany'] = array_intersect_key($one, array_flip($part['title']));
        }

        if (isset($part['options']['manyToOne']) && empty($part['options']['rows'])) {
            /** @var Model $linkModelClass1 */
            list($linkFieldName, $linkModelClass1) = $part['options']['manyToOne'];

            $values[$part['value']] = $linkModelClass1::createQueryBuilder()
                ->eq([$linkFieldName => $values[$part['name']]])
                ->group($linkFieldName)
                ->func(['GROUP_CONCAT' => $part['value']], '"",' . $part['value'])
                ->getSelectQuery('/pk')
                ->getValue($part['value']);
        }

        if (isset($part['options']['manyToMany'])) {
            /** @var Model $linkModelClass */
            /** @var Model $modelClass */
            list($modelClass, $linkFieldName, $linkModelClass, $fkFieldName, $linkFkFieldName) = $part['options']['manyToMany'];

            if (empty($values[$part['name']])) {
                $values[$part['name']] = 0;
            }
            if(array_key_exists('is_nested_sets', $part['options']) && $part['options']['is_nested_sets'] === true){
                $part['options']['rows'] = [0 => [$part['value'] => null, $modelClass::getFkFieldName() => null, $modelClass::getPkFieldName() => 0, $part['title'] => '', 'level' => 0]] +
                    $modelClass::createQueryBuilder()
                        ->left($linkModelClass, [$fkFieldName => $part['value']], $linkModelClass::getClassName() . '.' . $linkFieldName . '=' . $modelClass::getClassName() . '.' . $modelClass::getPkColumnName() . ' AND ' . $linkModelClass::getClassName() . '.' . $linkFkFieldName . '=' . $value)
                        ->group()
                        ->asc('left_key')
                        ->getSelectQuery([$part['title'], 'level'])
                        ->getRows();
                foreach($part['options']['rows'] as $key => $row){
                    $level = ($row['level'] - 1) * 4;
                    if($level < 0){
                        $level = 0;
                    }
                    $part['options']['rows'][$key]['category_name'] = str_repeat('&nbsp;', $level) . $row['category_name'];
                }
            } else {
                $part['options']['rows'] = [0 => [$part['value'] => null, $modelClass::getFkFieldName() => null, $modelClass::getPkFieldName() => 0, $part['title'] => '']] +
                    $modelClass::createQueryBuilder()
                        ->left($linkModelClass, [$fkFieldName => $part['value']], $linkModelClass::getClassName() . '.' . $linkFieldName . '=' . $modelClass::getClassName() . '.' . $modelClass::getPkColumnName() . ' AND ' . $linkModelClass::getClassName() . '.' . $linkFkFieldName . '=' . $value)
                        ->group()
                        ->getSelectQuery($part['title'])
                        ->getRows();

            }

            $manyToMany = array_filter($part['options']['rows'], function ($item) use ($value, $valueFieldName) {
                return $item[$valueFieldName] == $value;
            });

            $part['manyToMany'] = implode(', ', array_column($manyToMany, $part['title']));
            if(array_key_exists('is_nested_sets', $part['options']) && $part['options']['is_nested_sets'] === true){
                $part['manyToMany'] = str_replace('&nbsp;', '', $part['manyToMany']);
            }
        }

        if (isset($part['options']['oneToManyToMany'])) {
            /** @var Model $linkModelClass1 */
            /** @var Model $linkModelClass2 */
            list($linkModelClass1, $linkFieldName, $linkModelClass2) = $part['options']['oneToManyToMany'];
            $model = $linkModelClass1::getModel($values[$part['name']], $linkFieldName);
            $model = $model ? $model->fetchOne($linkModelClass2, $part['value'], true) : null;
            $values[$part['value']] = $model ? $model->get($part['value'], false) : '&nbsp;';
        }

        if ($part['value'] == $partName) {
            $paramValue = array_key_exists($part['value'], $values) ? $values[$part['value']] : null;

            if ($paramValue === null && isset($part['options']['default'])) {
                $paramValue = $part['options']['default'];
            }

            $part['params'] = [$part['name'] => $paramValue];
        } else {
            $part['params'] = [
                $part['name'] => array_key_exists($part['value'], $values) ? $values[$part['value']] : $part['value'],
                $part['partName'] => array_key_exists($partName, $values) ? $values[$partName] : null
            ];
        }

        if (isset($part['options']['params'])) {
            foreach ((array)$part['options']['params'] as $key => $value) {
                if (is_int($key)) {
                    $key = $value;
                }

                if (is_string($value)) {
                    $part['params'][$key] = $key == $value
                        ? (array_key_exists($value, $values) ? $values[$value] : null)
                        : (array_key_exists($value, $values) ? $values[$value] : $value); //(isset($part['options']['default']) ? $part['options']['default'] : $value)
                } else {
                    $part['params'][$key] = $value;
                }
            }
            unset($part['options']['params']);
        }

        if (isset($part['options']['dateFormat'])) {
            $date = array_key_exists('dateTimezone', $part['options'])
                ? new DateTime($part['params'][$part['name']], new DateTimeZone($part['options']['dateTimezone'] ?: 'Europe/Moscow'))
                : new DateTime($part['params'][$part['name']]);

            $part['params'][$part['name']] = $date->format($part['options']['dateFormat']);

            unset($part['options']['dateFormat']);
        }

        if (isset($part['options']['valueTemplate'])) {
            if ($part['options']['valueTemplate'] === true) {
                $part['options']['valueTemplate'] = $partName;
            }

            if ($part['resource']) {
                $part['options']['valueTemplate'] = $part['resource']->get($part['options']['valueTemplate'], $part['params']);
            }

            if ($render = strstr($part['options']['valueTemplate'], '/', true)) {
                $renderClass = Render::getClass($render);
                if (Loader::load($renderClass, false)) {
                    $part['options']['valueTemplate'] = substr($part['options']['valueTemplate'], strlen($render) + 1);
                } else {
                    $renderClass = Replace::getClass();
                }
            } else {
                $renderClass = Replace::getClass();
            }

            $part['params'][$part['title']] =
                $renderClass::getInstance()->fetch(
                    $part['options']['valueTemplate'],
                    $part['params'],
                    null,
                    Render::TEMPLATE_TYPE_STRING
                );

            unset($part['options']['valueTemplate']);
        }

        $part['dataParams'] = Json::encode($part['params']);
    }

    /**
     * @param Render $renderClass
     * @param array $data
     * @return string
     */
    public function renderExternal($renderClass, array $data)
    {
        $start = microtime(true);
        $data['time'] = microtime(true) - $start;
        $data['widget'] = $this;
        return $renderClass::getInstance()->fetch($this->getTemplate(), $data);
    }

    public function renderPart(array $part)
    {
        /** @var Render $renderClass */
        $renderClass = empty($part['renderClass'])
            ? Php::getClass()
            : $part['renderClass'];

        $part['widgetId'] = $this->getWidgetId();
        $part['partId'] = $part['widgetId'] . '_' . $part['partName'];

        return $renderClass::getInstance()->fetch($part['template'], $part);
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    protected function getRenderEvent()
    {
        return [
            'action' => Render::class,
            'data' => ['widgets' => [$this->getParentWidgetId() => $this->getParentWidgetClass()]],
            'url' => [Router::getInstance()->getName(), $this->getDataParams(), true],
            'method' => 'GET'
        ];
    }
}
