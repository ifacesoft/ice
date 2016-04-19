<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Helper\Json;

class FormElement_Typehead extends FormElement_TextInput
{
    private $items = null;
    private $itemId = null;
    private $itemTitle = null;
    private $itemValue = null;

    /**
     * @return null
     */
    public function getItemId()
    {
        return $this->setItemId($this->getOption('itemId', 'id'));
    }

    /**
     * @param string $itemId
     * @return string
     */
    protected function setItemId($itemId)
    {
        return $this->itemId = $itemId;
    }

    /**
     * @return null
     */
    public function getItemTitle()
    {
        return $this->setItemTitle($this->getOption('itemTitle', 'name'));
    }

    /**
     * @param string $itemTitle
     * @return string
     */
    protected function setItemTitle($itemTitle)
    {
        return $this->itemTitle = $itemTitle;
    }

    /**
     * @return null
     */
    public function getItems()
    {
        if ($this->items !== null) {
            return $this->items;
        }

        return array_values($this->getOption('rows', []));
    }

    /**
     * @param array $items
     * @return array
     */
    protected function setItems(array $items)
    {
        return $this->items = $items;
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
        return $this->get($this->getName() . '_typeahead') ? $this->get($this->getName() . '_typeahead') : '';
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $this->params[$this->getName() . '_typeahead'] = $this->getFromProviders($this->getName() . '_typeahead', $values);
    }
}