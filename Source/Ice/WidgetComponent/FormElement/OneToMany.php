<?php

namespace Ice\WidgetComponent;

class FormElement_OneToMany extends FormElement
{
    /**
     * @var string
     */
    private $placeholder = null;

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }
    
    public function __construct($name, array $options, $template, $componentName)
    {
        parent::__construct($name, $options, $template, $componentName);

        $this->placeholder = $this->getPlaceholder($options);
    }
    
    public function initValues() {
        if (isset($part['options']['oneToMany']) && empty($part['options']['rows'])) {
            $part['options']['oneToMany'] = (array)$part['options']['oneToMany'];
            $modelClass = array_shift($part['options']['oneToMany']);
            $fieldName = $modelClass::getPkFieldName();

            $firstRow = [$part['value'] => 0];

            $joinModelClasses = [$modelClass];

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $modelClass::createQueryBuilder();

            $part['title'] = (array)$part['title'];

            $fields = [];

            foreach ($part['title'] as $fieldModelClass => $fieldNames) {
                if (is_int($fieldModelClass)) {
                    $fieldModelClass = $modelClass;
                }

                if (!in_array($fieldModelClass, $joinModelClasses)) {
                    $queryBuilder->inner($fieldModelClass, '*');
                    $joinModelClasses[] = $fieldModelClass;
                }

                if ($fieldModelClass != $modelClass) {
                    $queryBuilder->asc($fieldNames, $fieldModelClass);
                }

                foreach ((array)$fieldNames as $field) {
                    $firstRow[$field] = '';
                    $fields[] = $field;
                }
            }

            $part['options']['rows'] = array_merge(
                [$firstRow],
                $queryBuilder
                    ->getSelectQuery(array_merge([$fieldName => $part['value']], isset($part['title'][$modelClass]) ? (array)$part['title'][$modelClass] : (array)reset($part['title'])))
                    ->getRows()
            );

            $oneToMany = array_filter($part['options']['rows'], function ($item) use ($value, $valueFieldName) {
                return $item[$valueFieldName] == $value;
            });

            $part['title'] = $fields;

            $one = $oneToMany ? reset($oneToMany) : array_fill_keys((array)$part['title'], '');

            $part['oneToMany'] = array_intersect_key($one, array_flip($part['title']));
        }
    }
}