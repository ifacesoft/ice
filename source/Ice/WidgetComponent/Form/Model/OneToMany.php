<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\Core\Widget as Core_Widget;

class Form_Model_OneToMany extends FormElement_Chosen
{
    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        if (!isset($options['comparison'])) {
            $options['comparison'] = '=';
        }

        parent::__construct($componentName, $options, $template, $widget);
    }

    /**
     * @param array $fieldNames
     * @param array $filter
     * @return array
     * @throws \Ice\Core\Exception
     */
    public function getItems($fieldNames = [], $filter = [])
    {
        /** @var Model $modelClass */
        $itemModel = $this->getItemModel();

        if (is_array($itemModel)) {
            list($modelClass, $tableAlias) = $itemModel;
        } else {
            $modelClass = $itemModel;
            $tableAlias = null;
        }

        $queryBuilder = $modelClass::createQueryBuilder($tableAlias);

        if ($sort = $this->getItemSort()) {
            foreach ((array)$sort as $fieldName => $order) {
                if (is_int($fieldName)) {
                    $fieldName = $order;
                    $order = QueryBuilder::SQL_ORDERING_ASC;
                }

                if (strtoupper($order) == QueryBuilder::SQL_ORDERING_DESC) {
                    $queryBuilder->desc($fieldName, $itemModel);
                } else {
                    $queryBuilder->asc($fieldName, $itemModel);
                }
            }
        }

        $params = array_filter((array)$this->getOption('params', []), function ($item) {
            return is_string($item);
        });

        $itemTitle = $this->getItemTitle();

        if (is_string($itemTitle)) {
            if (strpos($itemTitle, '{$') === false) {
                $itemTitle = array_merge((array)$itemTitle, (array)$fieldNames);
            } else {
                $itemTitle = array_diff(array_merge($params, (array)$fieldNames), [$this->getValueKey()]);
            }
        } else {
            $itemTitle = array_diff(array_merge($params, (array)$fieldNames, (array)$itemTitle), [$this->getValueKey()]);
        }

        if ($itemFilter = array_merge($this->getItemFilter(), $filter)) {
            $queryBuilder->eq($itemFilter, $itemModel);
        }

        $this->setOption('items', $queryBuilder->getSelectQuery(array_merge((array)$this->getItemKey(), $itemTitle), $itemModel)->getRows());

        return parent::getItems();
    }

    /**
     * @return Model
     */
    public function getItemModel()
    {
        return $this->getOption('itemModel');
    }

    public function getItemKey()
    {
        /** @var Model $itemModel */
        $itemModel = $this->getItemModel();

        if (is_array($itemModel)) {
            list($modelClass, $tableAlias) = $itemModel;
        } else {
            $modelClass = $itemModel;
            $tableAlias = null;
        }

        return $this->getOption('itemKey', $modelClass::getPkFieldName());
    }

    public function join(QueryBuilder $queryBuilder, $fieldNames = [])
    {
        /** @var Model|string $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModel());

        $params = array_filter((array)$this->getOption('params', []), function ($item) {
            return is_string($item);
        });

        $itemTitle = $this->getItemTitle();

//        if (is_string($itemTitle)) {
            if (strpos($itemTitle, '{$') === false) {
                $itemTitle = array_merge((array)$itemTitle, (array)$fieldNames);
            } else {
                $itemTitle = array_diff(array_merge($params, (array)$fieldNames), [$this->getValueKey()]);
            }
//        } else {
//            $itemTitle = array_diff(array_merge($params, (array)$fieldNames, (array)$itemTitle), [$this->getValueKey()]);
//        }

        return $queryBuilder->left([$modelClass, $tableAlias], array_merge((array)$this->getItemKey(), $itemTitle));
    }

    public function save(Model $model)
    {
        $save = parent::save($model);

        if (empty($save[$this->getName()])) {
            $save[$this->getName()] = null;
        }

        return $save;
    }

    public function getItemSort()
    {
        return $this->getOption('itemSort', []);
    }
}