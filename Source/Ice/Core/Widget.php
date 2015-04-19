<?php
namespace Ice\Core;

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
     * @return Widget_Menu|Widget_Data|Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    public static function create()
    {
        $class = self::getClass();

        return new $class();
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
     * @param $url
     * @param $action
     * @param $block
     * @param string $event
     * @return Widget_Form|Widget_Menu|Widget_Data
     */
    public function setAjax($url, $action, $block, $event = Widget_Form::SUBMIT_EVENT_ONCHANGE)
    {
        $this->url = $url;
        $this->action = $action;
        $this->block = $block;
        $this->event = $event;
        return $this;
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

    protected function addValue($key, $value)
    {
        $this->values[$key] = $value;
    }
}
