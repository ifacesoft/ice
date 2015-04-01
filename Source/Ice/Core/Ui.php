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
    private $classes = [];
    private $style = null;
    private $template = null;

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     *
     * @return Ui
     */
    public function classes(array $classes)
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
     * @return Ui
     */
    public function style($style)
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
     * @return Ui
     */
    public function template($template)
    {
        $this->template = $template;
        return $this;
    }
}