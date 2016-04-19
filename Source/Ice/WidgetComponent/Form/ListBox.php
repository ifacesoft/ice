<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.04.16
 * Time: 16:08
 */

namespace Ice\WidgetComponent;

class Form_ListBox extends FormElement
{
    public function getItems()
    {
        return $this->getOption('items', []);
    }
    
    public function getItemKey() {
        return $this->getOption('itemKey', 'itemKey');
    }

    public function getItemTitle() {
        return $this->getOption('itemTitle', 'itemTitle');
    }
}