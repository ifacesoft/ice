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

    public function getItems($fieldNames = [])
    {
        /** @var Model $modelClass */
        $modelClass = $this->getItemModel();

        $queryBuilder = $modelClass::createQueryBuilder();

        if ($sort = $this->getOption('itemSort')) {
            foreach ((array)$sort as $fieldName => $order) {
                if (is_int($fieldName)) {
                    $fieldName = $order;
                    $order = QueryBuilder::SQL_ORDERING_ASC;
                }

                if (strtoupper($order) == QueryBuilder::SQL_ORDERING_DESC) {
                    $queryBuilder->desc($fieldName);
                } else {
                    $queryBuilder->asc($fieldName);
                }
            }
        }

        $params = array_filter((array)$this->getOption('params', []), function ($item) {
            return is_string($item);
        });

        $itemTitle = strpos($this->getItemTitle(), '{$') === false
            ? array_merge([$this->getItemTitle()], (array)$fieldNames)
            : array_diff(array_merge($params, (array)$fieldNames), [$this->getValueKey()]);

        $this->setOption('items', $queryBuilder->getSelectQuery(array_merge((array)$this->getItemKey(), $itemTitle))->getRows());

        return parent::getItems();
    }

    /**
     * @return Model
     */
    public function getItemModel()
    {
        return $this->getOption('itemModel');
    }

    public function join(QueryBuilder $queryBuilder)
    {
        /** @var Model|string $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModel());

        $params = array_filter((array)$this->getOption('params', []), function ($item) {
            return is_string($item);
        });

        $itemTitle = strpos($this->getItemTitle(), '{$') === false
            ? (array)$this->getItemTitle()
            : array_diff($params, [$this->getValueKey()]);

        return $queryBuilder->left([$modelClass => $tableAlias], array_merge((array)$this->getItemKey(), $itemTitle));
    }

    public function save(Model $model)
    {
        $save = parent::save($model);

        if (empty($save[$this->getName()])) {
            return [];
        }

        return $save;
    }
}