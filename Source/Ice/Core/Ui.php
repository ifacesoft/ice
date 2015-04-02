<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 4/1/15
 * Time: 2:43 PM
 */

namespace Ice\Core;

trait Ui {
    /** @var array  */
    private $_classes = [];
    private $_style = null;
    private $_template = null;

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * @param array $classes
     *
     * @return Ui_Form|Ui_Menu|Ui_Data
     */
    public function classes(array $classes)
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
}