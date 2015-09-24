<?php

namespace Ice\Core;

use Ice\Core;

abstract class Widget_Menu extends Widget
{
    const ITEM_LINK = 'Item_Link';
    const ITEM_COLLAPSE = 'Item_Collapse';
    const ITEM_BUTTON = 'Item_Button';
    const ITEM_DROPDOWN = 'Item_Dropdown';

    protected $defaultOptions = [];

    public function link($name, $title, array $options = [], $template = 'Link')
    {
        return $this->addPart($name, $title, $options, $template, Widget_Menu::ITEM_LINK);
    }

    public function collapse($name, $title, array $options = [], $template = 'Collapse')
    {
        return $this->addPart($name, $title, $options, $template, Widget_Menu::ITEM_COLLAPSE);
    }

    public function button($name, $title, array $options = [], $template = 'Button')
    {
        return $this->addPart($name, $title, $options, $template, Widget_Menu::ITEM_BUTTON);
    }

    public function dropdown($name, $title, array $options = [], $template = 'Dropdown')
    {
        return $this->addPart($name, $title, $options, $template, Widget_Menu::ITEM_DROPDOWN);
    }
}
