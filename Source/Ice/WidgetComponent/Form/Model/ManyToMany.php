<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
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

    public function getItems()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getItemModelClass();

        $this->setOption('items', $modelClass::getItems($this->getItemKey(), $this->getItemTitle()));

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

    public function getItemModelClass()
    {
        return $this->getOption('itemModelClass');
    }

    public function save(Model $model)
    {
        /** @var Model $itemModelClass */
        $itemModelClass = $this->getItemModelClass();

        $oldValues = $itemModelClass::createQueryBuilder()
            ->inner($this->getLinkModelClass())
            ->eq([$this->getLinkKey() => $model->getPkValue()], $this->getLinkModelClass())
            ->getSelectQuery($this->getItemKey())
            ->getColumn();

        $values = array_filter($this->getValue(), function ($item) {
            return !empty($item);
        });

        $add = array_diff($oldValues, $values);
        $remove = array_diff($values, $oldValues);

        $model
            ->removeLinks($this->getLinkModelClass(), $this->getLinkKey(), $this->getLinkForeignKey(), $add)
            ->addLinks($this->getLinkModelClass(), $this->getLinkKey(), $this->getLinkForeignKey(), $remove);

        return parent::save($model);
    }

    public function getLinkModelClass()
    {
        return $this->getOption('linkModelClass');
    }

    public function getLinkKey()
    {
        return $this->getOption('linkKey');
    }

    public function getLinkForeignKey()
    {
        return $this->getOption('linkForeignKey');
    }

    public function join(QueryBuilder $queryBuilder) {
        /** @var Model|string $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModelClass());

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        $queryBuilder->left([$modelClass => $tableAlias])
            ->func(['GROUP_CONCAT' => $this->getItemKeysName()], 'DISTINCT ' . $tableAlias . '.' . $fieldColumnMap[$this->getItemKey()], $modelClass)
            ->func(['GROUP_CONCAT' => $this->getItemTitlesName()], 'DISTINCT ' . $tableAlias . '.' . $fieldColumnMap[$this->getItemTitle()], $modelClass);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $queryBuilder)
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $queryBuilder->getModelClassTableAlias($this->getItemModelClass());

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
}