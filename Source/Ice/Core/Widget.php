<?php
namespace Ice\Core;

use Ice\Exception\Access_Denied;
use Ice\Helper\Access;
use Ice\Helper\Arrays;
use Ice\Helper\Emmet;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\String;
use Ice\Render\Php;
use Ice\Render\Replace;

abstract class Widget extends Container
{
    use Stored;
    use Rendered;

    /**
     * @var array rows of values
     */
    private $rows = [];

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

    private $template = null;
    private $renderClass = null;
    private $layout = null;

    private $dataParams = null;

    private $url = null;
    private $actionClass = null;
    private $viewClass = null;

    private $token = null;
    private $data = [];
    private $resource = null;
    private $event = 'Ice_Core_Widget.click($(this), null, \'GET\');';

    private $result = null;
    private $compiledResult = null;

    private $forViewId = null;

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

    /**
     * Widget config
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   1.1
     */
    protected static function config()
    {
        return [
            'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    protected function init(array $params) {
        if ($input = Input::get(self::getClass())) {
            $this->rows = [$input];
        }

        $this->token = crc32(String::getRandomString());

        $this->build($input);
    }

    protected abstract function build(array $input);

    /**
     * @return null
     */
    public function getForViewId()
    {
        return $this->forViewId;
    }

    /**
     * @param null $forViewId
     * @return $this
     */
    public function setForViewId($forViewId)
    {
        $this->forViewId = $forViewId;
        return $this;
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
     * @return array
     */
    public function getRows()
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
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionClass()
    {
        return $this->actionClass;
    }

    /**
     * @param string $actionClass
     * @return $this
     */
    public function setActionClass($actionClass)
    {
        $this->actionClass = Action::getClass($actionClass);
        return $this;
    }

    /**
     * @return string
     */
    public function getViewClass()
    {
        return $this->viewClass;
    }

    /**
     * @param string $viewClass
     * @return $this
     */
    public function setViewClass($viewClass)
    {
        $this->viewClass = View::getClass($viewClass);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bind(array $params)
    {
        if (empty($this->rows)) {
            $this->rows = [$params];
        } else {
            $this->rows[0] = array_merge($this->rows[0], $params);
        }

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

    public function queryBuilderPart(Query_Builder $queryBuilder)
    {
        foreach ($this->getParts() as $part) {
            if (isset($part['options']['widget'])) {
                $part['options']['widget']->queryBuilderPart($queryBuilder);
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

    /**
     * ```php
     * ->setResource(true)
     *
     * ->setResource(Resource::create(My\Class))
     *
     * ->setResource(My\Class)
     * ```
     *
     * @param Resource|string|boolean $resource
     * @return $this
     */
    public function setResource($resource)
    {
        if ($resource instanceof Resource) {
            $this->resource = $resource;
        } else {
            if ($resource === true) {
                $resource = $this->getTemplate($this->template);
            }

            $this->resource = Resource::create($resource);
        }

        return $this;
    }

    public function getResource($resource = null)
    {
        if ($resource instanceof Resource) {
            return $resource;
        }

        if ($resource === true || (is_array($resource) && !isset($resource['class']))) {
            $resource = ['class' => $this->getTemplate($this->template)];
        }

        if (is_string($resource)) {
            $resource = ['class' => $resource];
        }

        if (isset($resource['class'])) {
            return Resource::create($resource['class']);
        }

        return $this->resource;
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

        $rows = $this->rows ? $this->rows : [[]];

        $offset = $this->getOffset();

        foreach ($rows as $values) {
            $row = [];

            foreach ($this->getParts() as $partName => $part) {
                try {
                    if (isset($part['options']['access'])) {
                        Access::check($part['options']['access']);
                    }
                } catch (Access_Denied $e) {
                    continue;
                }

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
                        $part['options']['href'] = $this->getFullUrl(Router::getInstance()->getUrl($routeName, $routeParams));
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
                                Replace::getInstance()->fetch($template, $params, Render::TEMPLATE_TYPE_STRING);
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
                foreach (['onclick', 'onchange'] as $event) {
                    if (array_key_exists($event, $part['options'])) {
                        if ($part['options'][$event] === true) {
                            $part['options'][$event] = $this->getEvent();
                            continue;
                        }

                        if (in_array($part['options'][$event], ['GET', 'POST'])) {
                            $part['options'][$event] = 'Ice_Core_Widget.click($(this), null, \'' . $part['options'][$event] . '\');';
                        }
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

        return $this->compiledResult = [
            'result' => $this->getResult(),
            'widgetName' => $this->getInstanceKey(),
            'widgetData' => $this->getData(),
            'widgetClassName' => $widgetClassName,
            'widgetResource' => $this->getResource(),
            'classes' => $this->getClasses(),
            'url' => $this->getUrl(),
            'dataToken' => $this->getToken(),
            'dataAction' => $this->getActionClass(),
            'dataView' => $this->getViewClass(),
            'dataWidget' => $widgetClass,
            'dataUrl' => $this->getFullUrl($this->getUrl()),
            'dataFor' => $this->getForViewId()
        ];
    }

    public function render()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        $template = $widgetClass::getTemplate($this->template);

        $widgetContent = $widgetClass::getRender($this->renderClass)
            ->fetch($template, $this->getCompiledResult());

        $layout = $widgetClass::getLayout($this->layout);

        return $layout
            ? Emmet::translate($layout . '{{$widgetContent}}', ['widgetContent' => $widgetContent])
            : $widgetContent;
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
        if (!empty($options['rewrite']) && isset($this->parts[$partName])) {
            unset($this->parts[$partName]);
        }

        $widgetClass = get_class($this);

        $this->parts[$partName] = [
            'options' => Arrays::defaults($this->defaultOptions, $options),
            'template' => $template,
            'element' => $widgetClass::getClassName() . '_' . $element
        ];

        if (isset($this->parts[$partName]['options']['default']) && $this->getValue($partName) === null) {
            $this->bind([$partName => $this->parts[$partName]['options']['default']]);
        }

        return $this;
    }

    /**
     * Compiled widget parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   1.0
     */
    public function getParts()
    {
        $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
        $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';

        $parts = [];

        $filterParts = $this->getFilterParts();

        foreach ($this->parts as $partName => $part) {
            if (!empty($filterParts) && !isset($filterParts[$partName])) {
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

        $row = empty($this->rows) ? [] : reset($this->rows);

        foreach ($row as $partName => $value) {
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
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
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
     * @param $name
     * @param array $options
     * @param string $template
     * @return $this
     */
    public function widget($name, array $options = [], $template = 'Ice\Widget\Widget')
    {
        if ($options['widget']->getResource()) {
            $options['widget']->setResource($this->getResource());
        }

        if ($options['widget']->getUrl()) {
            $options['widget']->setUrl($this->getUrl());
        }

        $options['widget']->setActionClass($this->getActionClass());
        $options['widget']->setViewClass($this->getViewClass());

        $options['widget']->init($options['widget']->getValues());

        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    protected function getWidget($widgetClass, $widgetName, array $params = []) {
        $params['forViewId'] = $this->getForViewId();

        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($widgetClass);

        return $widgetClass::getInstance($widgetName, null, $params);
    }
}
