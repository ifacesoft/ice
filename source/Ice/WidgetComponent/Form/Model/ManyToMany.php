<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\Core\Widget as Core_Widget;

class Form_Model_ManyToMany extends FormElement_Chosen
{
    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        $options['multiple'] = true;

        parent::__construct($componentName, $options, $template, $widget);
    }

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => FormElement_Chosen::class, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * @param array $fieldNames
     * @param array $filter
     * @return array
     * @throws \Ice\Core\Exception
     *
     * @todo: Это полный дупликат из OneToMany
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

    public function getItemKeysName($componentName = null)
    {
        return ($componentName ? $componentName : $this->getComponentName()) . '_keys';
    }

    public function getItemTitlesName($componentName = null)
    {
        return ($componentName ? $componentName : $this->getComponentName()) . '_titles';
    }

    public function getItemModel()
    {
        return $this->getOption('itemModel');
    }

    /**
     * @param Model $model
     * @return array
     * @throws \Ice\Core\Exception
     */
    public function save(Model $model)
    {
        /** @var Model $itemModel */
        $itemModel = $this->getItemModel();

        $oldValues = $itemModel::createQueryBuilder()
            ->inner($this->getLinkModel())
            ->eq([$this->getLinkKey() => $model->getPkValue()], $this->getLinkModel())
            ->getSelectQuery($this->getItemKey())
            ->getColumn();

        $values = array_filter($this->getValue(), function ($item) {
            return !empty($item);
        });

        $model
            ->removeLinks(
                $this->getLinkModel(),
                $this->getLinkKey(),
                $this->getLinkForeignKey(),
                array_diff($oldValues, $values)
            )
            ->addLinks(
                $this->getLinkModel(),
                $this->getLinkKey(),
                $this->getLinkForeignKey(),
                array_diff($values, $oldValues)
            );

        return parent::save($model);
    }

    public function getLinkModel()
    {
        return $this->getOption('linkModel');
    }

    public function getLinkKey()
    {
        return $this->getOption('linkKey');
    }

    public function getLinkForeignKey()
    {
        return $this->getOption('linkForeignKey');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     * @throws \Ice\Core\Exception
     */
    public function join(QueryBuilder $queryBuilder) {
        /** @var Model|string $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModel());

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        $queryBuilder
            ->left($this->getLinkModel())
            ->left($this->getItemModel())
            ->func(
                ['GROUP_CONCAT' => $this->getItemKeysName()],
                'DISTINCT ' . $tableAlias . '.' . $fieldColumnMap[$this->getItemKey()],
                $modelClass
            )
            ->func(
                ['GROUP_CONCAT' => $this->getItemTitlesName()],
                'DISTINCT ' . $tableAlias . '.' . $fieldColumnMap[$this->getItemTitle()],
                $modelClass
            );

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     * @throws \Ice\Core\Exception
     */
    public function filter(QueryBuilder $queryBuilder)
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModel());

        foreach ((array)$this->get($this->getName()) as $value) { // todo: Возможно это переписать на parent::filter - тут ничего сложного (см OneToMany)
            if ($value) {
                $queryBuilder->pk($value, $modelClass);
            }
        }

        return $queryBuilder;
    }

    protected function getValidValue()
    {
        if ($value = parent::getValidValue()) {
            return $value;
        }

        return explode(',', $this->get($this->getItemKeysName(), ''));
    }

    public function getItemSort()
    {
        return $this->getOption('itemSort', []);
    }
}