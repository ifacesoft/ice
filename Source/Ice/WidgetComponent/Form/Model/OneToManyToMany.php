<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;

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

    protected function buildParams($values)
    {
        parent::buildParams($values);
    }

    public function filter(QueryBuilder $queryBuilder)
    {
        parent::filter($queryBuilder);
    }

    public function getManyLabel() {
        return $this->getLabel() . '_many';
    }

    public function getPlaceholderManyAttribute() {
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
            $rows = [[$this->getManyItemKey() => null, $this->getManyItemTitle() => 'sds']] + $rows;
        }

        return $rows;
    }

    public function getManyValueKey() {
        return $this->getOption('manyValueKey');
    }

    public function getManyValue($encode = null)
    {
        $value = $this->get($this->getManyValueKey());

        return $value && $encode ? htmlentities($value) : '';
    }
}