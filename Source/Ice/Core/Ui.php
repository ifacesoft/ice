<?php
namespace Ice\Core;

abstract class Ui
{
    use Stored;

    private $_values = null;
    private $_classes = '';
    private $_style = '';
    private $_template = null;
    private $_params = null;
    private $_url = null;
    private $_action = null;
    private $_block = null;
    private $_event = null;

    private function __construct()
    {
    }

    protected function addValue($key, $value)
    {
        $this->_values[$key] = $value;
    }

    /**
     * Create new instance of ui component
     *
     * @return Ui_Menu|Ui_Data|Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
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
        return $this->_classes;
    }

    /**
     * @param string $classes
     *
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function setClasses($classes)
    {
        $this->_classes = $classes;
        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->_style;
    }

    /**
     * @param string $style
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function setStyle($style)
    {
        $this->_style = $style;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @param string $template
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        if ($this->_params !== null) {
            return $this->_params;
        }

        return $this->_params = $this->getValues();
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getValues($name = null)
    {
        if ($name) {
            return isset($this->_values[$name]) ? $this->_values[$name] : null;
        }

        return $this->_values;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->_block;
    }

    /**
     * @param $url
     * @param $action
     * @param $block
     * @param string $event
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function setAjax($url, $action, $block, $event = Ui_Form::SUBMIT_EVENT_ONCHANGE)
    {
        $this->_url = $url;
        $this->_action = $action;
        $this->_block = $block;
        $this->_event = $event;
        return $this;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->_event;
    }

    public abstract function bind($key, $value);

    public abstract function render();

    public function setQueryResult(Query_Result $queryResult)
    {
    }
}