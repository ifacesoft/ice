<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\DataProvider\Request;
use Ice\Helper\Json;
use Ice\Core\Widget as Core_Widget;

class Form_Model_OneToManyToMany extends Form_Model_OneToMany
{
    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        if (is_array($options['manyItemModel'])) {
            list($modelClass, $tableAlias) = $options['manyItemModel'];
        } else {
            $modelClass = $options['manyItemModel'];
            $tableAlias = null;
        }

        $options['params'][$modelClass::getPkFieldName() . '_many'] = ['providers' => [Request::class, 'default'], 'default' => ''];

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
            'render' => ['template' => __CLASS__ . '_Chosen', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    public function getManyLabel()
    {
        $manyLabel = $this->getOption('manyLabel', true);

        if ($manyLabel === true) {
            $manyLabel = $this->getManyItemKey();
        }

        if (is_array($manyLabel)) {
            $manyLabel = reset($manyLabel);
        }

        $manyLabel .= '_many';

        if ($resource = $this->getResource()) {
            $manyLabel = ($resource->get($manyLabel, $this->get()));
        }

        return $manyLabel;
    }

    public function getPlaceholderManyAttribute($attributeName = 'placeholder')
    {
        return $this->getPlaceholderAttribute($attributeName, '_many');
    }

    public function getManyItems()
    {
        /** @var Model $manyItemModel */
        $manyItemModel = $this->getManyItemModel();

        /** @var Model $modelClass */
        if (is_array($manyItemModel)) {
            list($modelClass, $tableAlias) = $manyItemModel;
        } else {
            $modelClass = $manyItemModel;
            $tableAlias = null;
        }

        $queryBuilder = $modelClass::createQueryBuilder($tableAlias);

        if ($manyItemSort = $this->getManyItemSort()) {

            foreach ((array)$manyItemSort as $fieldName => $order) {
                if (is_int($fieldName)) {
                    $fieldName = $order;
                    $order = QueryBuilder::SQL_ORDERING_ASC;
                }

                if (strtoupper($order) == QueryBuilder::SQL_ORDERING_DESC) {
                    $queryBuilder->desc($fieldName, $manyItemModel);
                } else {
                    $queryBuilder->asc($fieldName, $manyItemModel);
                }
            }
        }

        if ($manyItemFilter = $this->getManyItemFilter()) {
            $queryBuilder->eq($manyItemFilter, $manyItemModel);
        }

        $manyItemTitle = $this->getManyItemTitle();
        $manyItemKey = $this->getManyItemKey();

        $rows = $queryBuilder
            ->getSelectQuery(array_merge((array)$manyItemKey, (array)$manyItemTitle), $manyItemModel)
            ->getRows();

        if ($this->getOption('required', false) === false) {
            if (is_array($manyItemTitle)) {
                $manyItemTitle = reset($manyItemTitle);
            }

            if (is_array($manyItemKey)) {
                $manyItemKey = reset($manyItemKey);
            }

            $rows = [[$manyItemKey => '', $manyItemTitle => '']] + $rows;
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
        /** @var Model $itemModel */
        $manyItemModel = $this->getManyItemModel();

        if (is_array($manyItemModel)) {
            list($modelClass, $tableAlias) = $manyItemModel;
        } else {
            $modelClass = $manyItemModel;
            $tableAlias = null;
        }

        return $this->getOption('manyItemKey', $modelClass::getPkFieldName());
    }

    public function getManyItemTitle()
    {
        $name = $this->getOption('manyItemTitle', 'name');

        if (is_array($name)) {
            return $name;
        }

        return htmlentities($name);
    }

    public function getManyItemFilter()
    {
        return $this->getOption('manyItemFilter', []);
    }

    public function getManyItemSort()
    {
        return $this->getOption('manyItemSort', []);
    }

    public function getItemsGroupJson()
    {
        $itemsGroup = [];

        $itemManyKey = $this->getItemManyKey();

        foreach ($this->getItems($itemManyKey) as $key => $item) {
            if (!isset($item[$itemManyKey])) {
                continue;
            }

            $itemsGroup[$item[$itemManyKey]][$key] = $item;
        }

        if ($this->getOption('required', false) === false) {
            $itemKey = $this->getItemKey();
            if (is_array($itemKey)) {
                $itemKey = reset($itemKey);
            }

            $itemTitle = $this->getItemTitle();

            if (is_array($itemTitle)) {
                $itemTitle = reset($itemTitle);
            }

            foreach ($itemsGroup as &$rows) {
                $rows = array_values([[$itemKey => '', $itemTitle => '']] + $rows);
            }
        }

        return Json::encode($itemsGroup);
    }

    public function getManyValue($encode = null)
    {
//        if ($value = $this->getValue()) {
//            /** @var Model $itemModel */
//            $itemModel = $this->getItemModel();
//
//            if (is_array($itemModel)) {
//                list($modelClass, $tableAlias) = $itemModel;
//            } else {
//                $modelClass = $itemModel;
//                $tableAlias = null;
//            }
//
//            return $modelClass::getSelectQuery($this->getManyValueKey(), ['/pk' => $value], null, null, $tableAlias)->getValue();
//        }

        $manyItemKey = $this->getManyItemKey();

        if (is_array($manyItemKey)) {
            $manyItemKey = reset($manyItemKey);
        }

        return $this->get($manyItemKey . '_many', '');

        return '';
    }

    public function join(QueryBuilder $queryBuilder, $fieldNames = []) {
        $itemManyKey = $this->getItemManyKey();

        if (is_array($itemManyKey)) {
            $itemManyKey = reset($itemManyKey);
        }

        $manyItemKey = $this->getManyItemKey();

        if (is_array($manyItemKey)) {
            $manyItemKey = reset($manyItemKey);
        }

        return parent::join($queryBuilder, array_merge([$itemManyKey => $manyItemKey . '_many'], $fieldNames));
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     * @throws \Ice\Core\Exception
     */
    public function filter(QueryBuilder $queryBuilder)
    {
        if (!$this->getValue()) {
            if ($itemManyValue = $this->getManyValue()) {
                $queryBuilder->in(
                    $this->getName(),
                    array_keys($this->getItems([], [$this->getItemManyKey() => $itemManyValue]))
                );
            }

            return $queryBuilder;
        }

        parent::filter($queryBuilder);

//        $option = array_merge($this->getOption(), $this->getOption('filter', []));
//
//        $value = $this->getValue();
//
//        if ($value === null || $value === '' || (is_array($value)) && empty($value)) {
//            return $queryBuilder;
//        }
//
//        if (!isset($option['comparison'])) {
//            $option['comparison'] = 'like';
//        }
//
//        if (!isset($option['modelClass'])) {
//            $option['modelClass'] = $queryBuilder->getModelClass();
//        }
//
//        foreach ((array)$value as $val) {
//            $val = html_entity_decode($val);
//
//            switch ($option['comparison']) {
//                case '=':
//                    $queryBuilder->eq([$this->getName() => $val], $option['modelClass']);
//                    break;
//                case 'like':
//                default:
//                    $queryBuilder->like($this->getName(), '%' . $val . '%', $option['modelClass']);
//            }
//        }

        return $queryBuilder;
    }

    public function getItemManyKey()
    {
        return $this->getOption('itemManyKey');
    }
}