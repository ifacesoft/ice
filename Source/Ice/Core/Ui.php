<?php
namespace Ice\Core;

trait Ui {
    use Stored;

    private $_key = null;
    private $_classes = '';
    private $_style = '';
    private $_template = null;
    private $_params = null;
    private $_url = null;
    private $_action = null;
    private $_block = null;

    /**
     * Create new instance of ui
     *
     * @param $key
     * @return Ui_Menu|Ui_Data|Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
     */
    protected static function create($key)
    {
        $class = self::getClass();
//
//        if ($key) {
//            $class .= '_' . $key;
//        }

        $menu = new $class();

        $menu->_key = $key;

        return $menu;
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
    public function classes($classes)
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
    public function style($style)
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
    public function template($template)
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

        return $this->_params = $this->getKey();
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
    public function getKey($name = null)
    {
        if ($name) {
            return isset($this->_key[$name]) ? $this->_key[$name] : null;
        }

        return $this->_key;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function url($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @param string $action
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function action($action)
    {
        $this->_action = addslashes($action);
        return $this;
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->_block;
    }

    /**
     * @param string $block
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function block($block)
    {
        $this->_block = $block;
        return $this;
    }
}