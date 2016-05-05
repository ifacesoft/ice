<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
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

    public function getItemTitle($item = null)
    {
        $itemTitle = $this->getOption('itemTitle');

        if (!$itemTitle) {
            throw new Error(['Option itemTitle for component {$0} not found', $this->getComponentName()]);
        }

        if ($item === null) {
            return $itemTitle;
        }
        
//        if ($resource = $this->getResource()) {
//            $itemTitle = $resource->get($itemTitle, $item);
//        } else {
            $itemTitle = $item[$itemTitle];
//        }
        
        return htmlentities($itemTitle);
    }

    /**
     * @return null
     */
    public function getItems()
    {
        return $this->getOption('required', false) === false
            ? [[$this->getItemKey() => null, $this->getItemTitle() => '']] + $this->getOption('rows', [])
            : $this->getOption('rows', []);
    }
}