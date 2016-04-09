<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\WidgetComponent;
use Ice\Core\Widget as Core_Widget;

class Widget extends WidgetComponent
{
    private $widget = null;

    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($name, $options, $template, $widget);

        $this->widget = $widget->getWidget($options['widget']);

//        try {
//            Access::check($options['widget']::getConfig()->gets('access'));
//        } catch (Access_Denied $e) {
//            return $this;
//        }

        if ($this->widget->getResource() === null) {
            $this->widget->setResourceClass($this->getResource());
        }

//        if (isset($options['indexOffset'])) {
//            $options['widget']->indexOffset += $options['indexOffset'];
//            unset($options['indexOffset']);
//        }

//        $this->setDataParams($options['widget']->getDataParams());
    }

    /**
     * @return $this|null
     */
    public function getWidget()
    {
        return $this->widget;
    }
}