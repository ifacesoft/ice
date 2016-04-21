<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

use Ice\Helper\Json;

class FormElement_Typehead extends FormElement_TextInput
{
    /**
     * @return null
     */
    public function getItemKey()
    {
        return $this->getOption('itemKey', 'id');
    }

    /**
     * @return null
     */
    public function getItemTitle()
    {
        return $this->getOption('itemTitle', 'name');
    }

    /**
     * @return null
     */
    public function getItems()
    {
        return $this->getOption('rows', []);
    }

    public function getItemsJson()
    {
        return Json::encode($this->getItems());
    }

    /**
     * @return null
     */
    public function getItemValue()
    {
        return $this->get($this->getName() . '_typeahead') ? htmlentities($this->get($this->getName() . '_typeahead')) : '';
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $this->params[$this->getName() . '_typeahead'] = $this->getFromProviders($this->getName() . '_typeahead', $values);
    }
}