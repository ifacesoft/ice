<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Helper\Json;

class Form_Model_OneToManyToMany extends Form_Model_OneToMany
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__ . '_Chosen', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    public function getManyLabel()
    {
        $manyLabel = $this->getOption('manyLabel', true);

        if ($manyLabel === true) {
            $manyLabel = $this->getManyValueKey();
        }

        if ($resource = $this->getResource()) {
            $manyLabel = ($resource->get($manyLabel, $this->get()));
        }

        return $manyLabel;
    }

    public function getManyValueKey()
    {
        return $this->getOption('manyValueKey');
    }

    public function getPlaceholderManyAttribute()
    {
        return $this->getPlaceholderAttribute() . '_many';
    }

    public function getManyItems()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getManyItemModel();

        $queryBuilder = $modelClass::createQueryBuilder();

//        if ($sort = $this->getOption('sort')) {
//            foreach ((array) $sort as $fieldName => $order) {
//                if (is_int($fieldName)) {
//                    $fieldName = $order;
//                    $order = QueryBuilder::SQL_ORDERING_ASC;
//                }
//
//                if (strtoupper($order) == QueryBuilder::SQL_ORDERING_DESC) {
//                    $queryBuilder->desc($fieldName);
//                } else {
//                    $queryBuilder->asc($fieldName);
//                }
//            }
//        }

        $rows = $queryBuilder->getSelectQuery([$this->getManyItemKey(), $this->getManyItemTitle()])->getRows();

        if ($this->getOption('required', false) === false) {
            $rows = [[$this->getManyItemKey() => null, $this->getManyItemTitle() => '']] + $rows;
        }

        return $rows;
    }

    /**
     * @return null
     */
    public function getManyItemModel()
    {
        return $this->getOption('manyItemModel');
    }

    public function getManyItemKey()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getManyItemModel();

        return $modelClass::getPkFieldName();
    }

    public function getManyItemTitle()
    {
        return htmlentities($this->getOption('manyItemTitle', 'name'));
    }

    public function getItemsGroupJson()
    {
        $itemsGroup = [];

        foreach ($this->getItems($this->getManyValueKey()) as $key => $item) {
            if (!isset($item[$this->getManyValueKey()])) {
                continue;
            }

            $itemsGroup[$item[$this->getManyValueKey()]][$key] = $item;
        }

        if ($this->getOption('required', false) === false) {
            foreach ($itemsGroup as &$rows) {
                $rows = array_values([[$this->getItemKey() => null, $this->getItemTitle() => '']] + $rows);
            }
        }

        return Json::encode($itemsGroup);
    }

    public function getManyValue($encode = null)
    {
        if ($value = $this->getValue()) {
            /** @var Model $itemModelClass */
            $itemModelClass = $this->getItemModel();

            return $itemModelClass::getModel($value, $this->getManyValueKey())->get($this->getManyValueKey(), null);
        }

        return '';
    }

    protected function buildParams(array $values)
    {
        parent::buildParams($values);
    }
}