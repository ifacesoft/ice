<?php
namespace Ice\Core;

use Doctrine\Common\Util\Debug;
use Ice\Data\Provider\Cli as Data_Provider_Cli;
use Ice\Data\Provider\Router as Data_Provider_Router;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Session as Data_Provider_Session;
use Ice\Exception\Error;
use Ice\Helper\Arrays;
use Ice\Helper\Emmet;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\View\Render\Php;

abstract class Widget
{
    use Stored;

    /**
     * Values of current widget
     *
     * @var array
     */
    private $values = [];

    /**
     * Widget parts (fields for Form, columns for Data, items for Menu)
     *
     * @var array
     */
    private $parts = [];
    private $classes = '';
    private $style = '';
    private $header = '';
    private $description = '';
    private $template = null;

    /**
     * Merged values from others widgets (widget known values of other widgets)
     *
     * @var array
     */
    private $params = [];
    private $url = null;
    private $action = null;
    private $block = null;
    private $layout = '';
    private $token = null;
    private $data = [];
    private $resource = null;
    private $event = 'Ice_Core_Widget.click($(this), null, \'GET\');';

    private $compiledParts = null;

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
    protected $defaultOptions = [];

    protected function __construct()
    {
    }

    /**
     * Create new instance of ui component
     *
     * @param $url
     * @param $action
     * @param null $block
     * @param array $data
     * @return Widget_Data|Widget_Form|Widget_Menu
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    public static function create($url, $action, $block = null, array $data = [])
    {
        $class = self::getClass();

        $widget = new $class();

        $widget->url = $url;
        $widget->action = $action;
        $widget->block = $block ? $block : Object::getName($action);
        $widget->data = $data;
        $widget->values = Input::get($class);

        $widget->token = crc32(String::getRandomString());

        return $widget;
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
     * @return Widget_Menu|Widget_Data|Widget_Form
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return Widget_Menu|Widget_Data|Widget_Form
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return Widget_Menu|Widget_Data|Widget_Form
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
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
     * @return Widget_Menu|Widget_Data|Widget_Form
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    public function bind(array $params)
    {
        if (isset($params['token'])) {
            $this->setToken($params['token']);
            unset($params['token']);
        }

        foreach ($params as $key => $value) {
            $this->addValue($key, $value);
        }

        return $this;
    }

    abstract public function setQueryResult(Query_Result $queryResult);

    abstract public function queryBuilderPart(Query_Builder $queryBuilder);

    public static function getConfig()
    {
        $repository = self::getRepository();

        if ($config = $repository->get('config')) {
            return $config;
        }

        /**
         * @var Widget $widgetClass
         */
        $widgetClass = self::getClass();

        $config = Config::create(
            $widgetClass,
            array_merge_recursive(
                $widgetClass::config(),
                Config::getInstance($widgetClass, null, false, -1)->gets()
            )
        );

        return $repository->set('config', $config);
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage() . ' - ' . $this->getClass() . ' (' . $e->getFile() . ':' . $e->getLine() . ')';
        }
    }

    public function getFullUrl($url)
    {
        $queryString = http_build_query($this->getParams());

        return $url . ($queryString ? '?' . $queryString : '');
    }

    public function getCompiledParts()
    {
        if ($this->compiledParts !== null) {
            return $this->compiledParts;
        }

        $this->compiledParts = [];

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetBaseClass = Object::getBaseClass($widgetClass, __CLASS__);
        $widgetBaseClassName = $widgetBaseClass::getClassName();

        $values = $this->getValues();

        foreach ($this->getParts() as $partName => $part) {
            if (isset($part['options']['access'])) {
                if (!Security::getInstance()->check($part['options']['access'])) {
                    continue;
                }
            }

            $part['widgetClassName'] = $widgetClassName;
            $part['widgetBaseClassName'] = $widgetBaseClassName;
            $part['token'] = $this->getToken();

            if (!empty($part['options']['resource'])) {
                if ($part['options']['resource'] instanceof Resource) {
                    $part['title'] = $part['options']['resource']->get($part['title']);
                } else if (is_array($part['options']['resource'])) {
                    if (isset($part['options']['resource']['class'])) {
                        $value = isset($part['options']['resource']['params'])
                            ? (array)$part['options']['resource']['params']
                            : [];

                        $part['title'] = $widgetClass::getResource()
                            ->get($part['title'], $value, $part['options']['resource']['class']);
                    } else {
                        $part['title'] = $widgetClass::getResource()
                            ->get($part['title'], $part['options']['resource'], $this->getTemplate());
                    }
                } else {
                    $part['title'] = $widgetClass::getResource()
                        ->get($part['title'], (array)$part['options']['resource'], $this->getTemplate());
                }
            }

            if (isset($part['options']['value'])) {
                throw new Error('deprecated value option');
            }

            $part['name'] = isset($part['options']['name']) ? $part['options']['name'] : $partName;

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
                if (isset($values[$part['name']])) {
                    $params = [$part['name'] => $values[$part['name']]];
                }
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

                $part['options']['href'] = $this->getFullUrl(Router::getInstance()->getUrl($routeName, $routeParams));

                if (isset($part['options']['href']) && !array_key_exists('active', $part['options'])) {
                    $part['options']['active'] = String::startsWith(Request::uri(), $part['options']['href']);
                }
            }

            foreach (['onclick', 'onchange', 'onsubmit'] as $event) {
                if (array_key_exists($event, $part['options'])) {
                    if ($part['options'][$event] === true) {
                        $part['options'][$event] = $this->event;
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

            $this->compiledParts[$partName] = Php::getInstance()->fetch($template, $part);
        }

        return $this->compiledParts;
    }

    public function walkOptions($function, $option)
    {
        array_walk($this->parts, $function, $option);
        return $this;
    }

    public function render()
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetBaseClass = Object::getBaseClass($widgetClass, __CLASS__);
        $widgetBaseClassName = $widgetBaseClass::getClassName();

        $widgetContent = Php::getInstance()->fetch(
            empty($this->getTemplate()) ? $widgetClass : $this->getTemplate(),
            [
                'parts' => $this->getCompiledParts(),
                'widgetData' => $this->getData(),
                'widgetClass' => $widgetClass,
                'widgetClassName' => $widgetClassName,
                'widgetBaseClassName' => $widgetBaseClassName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle(),
                'header' => $this->getHeader(),
                'description' => $this->getDescription(),
                'url' => $this->getUrl(),
                'token' => $this->getToken(),
                'dataJson' => Json::encode($this->getParams()),
                'dataAction' => $this->getAction(),
                'dataBlock' => $this->getBlock(),
                'dataUrl' => $this->getFullUrl($this->getUrl()),
                'onsubmit' => isset($this->onsubmit) ? $this->onsubmit : null
            ]
        );

        return $this->getLayout()
            ? Emmet::translate($this->getLayout() . '{{$widgetContent}}', ['widgetContent' => $widgetContent])
            : $widgetContent;
    }

    public function addValue($key, $value)
    {
        if (is_array($value)) {
            $this->values = array_merge($this->values, $value);
        } else {
            $this->values[$key] = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
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
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }


    public function button($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Button')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template);
    }

    public function link($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Link')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template);
    }

    protected function addPart($partName, $partTitle, array $options, $template)
    {
        $this->parts[$partName] = [
            'title' => $partTitle,
            'options' => Arrays::defaults($this->defaultOptions, $options),
            'template' => $template
        ];

        if (isset($this->parts[$partName]['options']['default']) && $this->getValue($partName) === null) {
            $this->addValue($partName, $this->parts[$partName]['options']['default']);
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
     * @version 1.0
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

        return $this->values;
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
    public function getHeader()
    {
        return $this->resource instanceof Resource ? $this->resource->get($this->header) : $this->header;
    }

    /**
     * @param string $header
     * @return Widget_Data|Widget_Form|Widget_Menu
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Widget_Data|Widget_Form|Widget_Menu
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Resource
     */
    public function getWidgetResource()
    {
        return $this->resource;
    }

    /**
     * @param Resource $resource
     * @return Widget_Data|Widget_Form|Widget_Menu
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
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
     * @return Widget_Data|Widget_Form|Widget_Menu
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }
}
