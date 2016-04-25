<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\QueryBuilder;

class Form_Model_OneToMany extends FormElement_Chosen
{
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

        $queryBuilder = $modelClass::createQueryBuilder();

        if ($sort = $this->getOption('sort')) {
            foreach ((array) $sort as $fieldName => $order) {
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

        $rows = $queryBuilder->getSelectQuery([$this->getItemKey(), $this->getItemTitle()])->getRows();

        if ($this->getOption('required', false) === false) {
            $rows = [[$this->getItemKey() => null, $this->getItemTitle() => '']] + $rows;
        }

        $this->setOption('rows', $rows);

        return parent::getItems();
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

//        $name = $this->getName();
//        $typeahead = $this->getName() . '_typeahead';
//
//        $typeaheadValue = $this->get($typeahead);
//
//        /** @var Model $modelClass */
//        $modelClass = $this->getItemModel();
//
//        if ($typeaheadValue === null || $typeaheadValue === '') {
//            if ($value = $this->get($name)) {
//                $model = $modelClass::getModel($value, [$modelClass::getPkFieldName(), $this->getItemTitle()]);
//
//                if ($model) {
//                    $this->set($typeahead, $model->get($this->getItemTitle()));
//
//                    return;
//                }
//            }
//
//            $this->set($name, null);
//            $this->set($typeahead, null);
//
//            return;
//        }
//
//        $typeaheadModel = $modelClass::getSelectQuery([$modelClass::getPkFieldName(), $this->getItemTitle()], [$this->getItemTitle() => $typeaheadValue])->getModel();
//
//        if ($typeaheadModel) {
//            $this->set($name, $typeaheadModel->getPkValue());
//        } else {
//            if ($this->getOption('itemAutoCreate', false)) {
//                $this->set($name, $modelClass::create([$this->getItemTitle() => $typeaheadValue])->save()->getPkValue());
//            } else {
//                $this->set($name, 0);
////            $this->set($typeahead, null); // не обнуляем - ищем по вхождению
//            }
//        }
    }

    public function filter(QueryBuilder $queryBuilder)
    {
//        parent::filter($queryBuilder);
//
//        $typeahead = $this->getName() . '_typeahead';
//
//        $typeaheadValue = $this->get($typeahead);
//
//        if ($typeaheadValue === null || $typeaheadValue === '') {
//            return;
//        }
//
//        $queryBuilder->like($this->getItemTitle(), '%' . $typeaheadValue . '%', $this->getItemModel());
    }
}