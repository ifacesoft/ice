<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

use Ice\Helper\Json;

class FormElement_Typehead extends Form_ListBox
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }
    
    public function getItemsJson()
    {
        return Json::encode(array_values($this->getItems()));
    }

    /**
     * @return null
     */
    public function getItemValue()
    {
        return $this->get($this->getName() . '_typeahead') ? htmlentities($this->get($this->getName() . '_typeahead')) : '';
    }

//    protected function buildParams(array $values)
//    {
//        parent::buildParams($values);
//
//        $this->params[$this->getName() . '_typeahead'] = $this->getFromProviders($this->getName() . '_typeahead', $values);
//    }
}