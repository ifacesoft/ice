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

    /**
     * @return null
     */
    public function getItemModelClass()
    {
        return $this->getOption('itemModelClass');
    }

    public function getItemKey()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getItemModelClass();

        return $modelClass::getPkFieldName();
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

    public function filter(QueryBuilder $queryBuilder)
    {
        $modelClass = $this->getItemModelClass();

        foreach ((array)$this->get($this->getName()) as $value) { // todo: Возможно это переписать на parent::filter - тут ничего сложного
            if ($value) {
                $queryBuilder->pk($value, $modelClass);
            }
        }

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

    protected function buildParams(array $values)
    {
        parent::buildParams($values);

        $name = $this->getName();

        if ($this->get($name, null) === null && isset($values['pk'])) {
            /** @var Model $itemModelClass */
            $itemModelClass = $this->getItemModelClass();

            $rows = $itemModelClass::createQueryBuilder()
                ->inner($this->getLinkModelClass())
                ->eq([$this->getLinkKey() => $values['pk']], $this->getLinkModelClass())
                ->getSelectQuery($this->getItemKey())
                ->getColumn();

            $this->set($this->getName(), empty($rows) ? [] : $rows);
        }

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