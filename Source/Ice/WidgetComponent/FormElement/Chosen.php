<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

use Ice\Exception\Error;

class FormElement_Chosen extends FormElement_TextInput
{
    public function getItemKey()
    {
        $itemKey = $this->getOption('itemKey');

        if (!$itemKey) {
            throw new Error(['Option itemKey for component {$0} not found', $this->getComponentName()]);
        }

        return $itemKey;
    }

    public function getItemTitle()
    {
        $itemTitle = $this->getOption('itemTitle', 'name');

        if (!$itemTitle) {
            throw new Error(['Option itemTitle for component {$0} not found', $this->getComponentName()]);
        }
        
        return htmlentities($itemTitle);
    }

    /**
     * @return null
     */
    public function getItems()
    {
        return $this->getOption('rows', []);
    }
}