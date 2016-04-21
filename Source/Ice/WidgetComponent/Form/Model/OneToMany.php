<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\QueryBuilder;

class Form_Model_OneToMany extends FormElement_Typehead
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => FormElement_Typehead::class, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * @return null
     */
    public function getItemModel()
    {
        return $this->getOption('itemModel');
    }

    public function getItemKey()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getItemModel();

        return $modelClass::getPkFieldName();
    }

    public function getItems()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getItemModel();

        $this->setOption('rows', $modelClass::getSelectQuery([$this->getItemKey(), $this->getItemTitle()])->getRows());

        return parent::getItems();
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $name = $this->getName();
        $typeahead = $this->getName() . '_typeahead';

        $typeaheadValue = $this->get($typeahead);

        /** @var Model $modelClass */
        $modelClass = $this->getItemModel();

        if ($typeaheadValue === null || $typeaheadValue === '') {
            if ($value = $this->get($name)) {
                $model = $modelClass::getModel($value, [$modelClass::getPkFieldName(), $this->getItemTitle()]);

                if ($model) {
                    $this->set($typeahead, $model->get($this->getItemTitle()));

                    return;
                }
            }

            $this->set($name, null);
            $this->set($typeahead, null);

            return;
        }

        $typeaheadModel = $modelClass::getSelectQuery([$modelClass::getPkFieldName(), $this->getItemTitle()], [$this->getItemTitle() => $typeaheadValue])->getModel();

        if ($typeaheadModel) {
            $this->set($name, $typeaheadModel->getPkValue());
        } else {
            if ($this->getOption('itemAutoCreate', false)) {
                $this->set($name, $modelClass::create([$this->getItemTitle() => $typeaheadValue])->save()->getPkValue());
            } else {
                $this->set($name, 0);
//            $this->set($typeahead, null); // не обнуляем - ищем по вхождению
            }
        }
    }

    public function filter(QueryBuilder $queryBuilder)
    {
        parent::filter($queryBuilder);

        $typeahead = $this->getName() . '_typeahead';

        $typeaheadValue = $this->get($typeahead);

        if ($typeaheadValue === null || $typeaheadValue === '') {
            return;
        }

        $queryBuilder->like($this->getItemTitle(), '%' . $typeaheadValue . '%', $this->getItemModel());
    }
}