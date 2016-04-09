<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;

class Pagination_Li extends HtmlTag
{
    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        $options['onclick'] = $this->getEvent();
        
        parent::__construct($name, $options, $template, $widget);
    }

}