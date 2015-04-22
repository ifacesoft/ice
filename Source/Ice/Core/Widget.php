<?php
namespace Ice\Core;

use Ice\Helper\Object;

abstract class Widget
{
    use Stored;

    private $values = null;
    private $classes = '';
    private $style = '';
    private $template = null;
    private $params = null;
    private $url = null;
    private $action = null;
    private $block = null;
    private $event = null;

    protected function __construct()
    {
    }

    /**
     * Create new instance of ui component
     *
     * @param $url
     * @param $action
     * @param null $block
     * @param null $event
     * @return Widget_Data|Widget_Form|Widget_Menu
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    public static function create($url, $action, $block = null, $event = null)
    {
        $class = self::getClass();

        $widget = new $class();

        $widget->url = $url;
        $widget->action = $action;
        $widget->block = $block ? $block : Object::getName($action);
        $widget->event = $event;

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
        if ($this->params !== null) {
            return $this->params;
        }

        return $this->getValues();
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getValues($name = null)
    {
        if ($name) {
            return isset($this->values[$name]) ? $this->values[$name] : null;
        }

        return (array)$this->values;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    abstract public function bind($key, $value);

    public function setQueryResult(Query_Result $queryResult)
    {
    }

    public function __toString()
    {
        return $this->render();
    }

    abstract public function render();

    public function addValue($key, $value)
    {
        $this->values[$key] = $value;
    }
}
