<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

class FormElement_Chosen extends FormElement_TextInput
{
    public function getItemKey()
    {
        return $this->getOption('itemKey', 'id');
    }

    public function getItemTitle()
    {
        return htmlentities($this->getOption('itemTitle', 'name'));
    }

    /**
     * @return null
     */
    public function getItems()
    {
        return $this->getOption('rows', []);
    }
}