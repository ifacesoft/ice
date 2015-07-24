<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Emmet;
use Ice\Helper\Json;
use Ice\View\Render\Php;

abstract class Widget_Menu extends Widget
{
    protected $defaultOptions = [];

    public function link($name, $title, array $options = [], $template = 'Ice\Core\Widget_Menu_Link')
    {
        return $this->addPart($name, $title, $options, $template);
    }

    public function collapse($name, $title, array $options = [], $template = 'Ice\Core\Widget_Menu_Collapse')
    {
        return $this->addPart($name, $title, $options, $template);
    }

    public function button($name, $title, array $options = [], $template = 'Ice\Core\Widget_Menu_Button')
    {
        return $this->addPart($name, $title, $options, $template);
    }

    public function dropdown($name, $title, array $options = [], $template = 'Ice\Core\Widget_Menu_Dropdown')
    {
        return $this->addPart($name, $title, $options, $template);
    }

    public function setQueryResult(Query_Result $queryResult)
    {
    }

    public function queryBuilderPart(Query_Builder $queryBuilder)
    {
    }
}
