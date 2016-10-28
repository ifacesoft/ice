<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;

class Form_Model_OneToMany extends FormElement_Chosen
{
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

        $fieldNames = strpos($this->getItemTitle(), '{$') === false
            ? array_merge([$this->getItemTitle()], (array)$fieldNames)
            : array_diff(array_merge(array_keys($this->get(null, [], false)), (array)$fieldNames), [$this->getValueKey()]);

        $this->setOption('items', $queryBuilder->getSelectQuery(array_merge((array)$this->getItemKey(), $fieldNames))->getRows());

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
        /** @var Model $modelClass */
        $modelClass = $this->getItemModel();

        return $modelClass::getPkFieldName();
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function filter(QueryBuilder $queryBuilder)
    {
        $modelClass = $this->getItemModel();

        $this->setOption('name', $modelClass::getPkFieldName());
        $this->setOption('comparison', '=');

        parent::filter($queryBuilder, $modelClass);
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

    public function save(Model $model)
    {
        $save = parent::save($model);

        if (empty($save[$this->getName()])) {
            return [];
        }

        return $save;
    }

    protected function buildParams(array $values)
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
}