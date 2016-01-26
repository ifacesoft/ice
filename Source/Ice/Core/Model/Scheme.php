<?php

namespace Ice\Core;

class Model_Scheme extends Config
{
    const ONE_TO_MANY = 'oneToMany';
    const MANY_TO_ONE = 'manyToOne';
    const MANY_TO_MANY = 'manyToMany';

    /**
     * @param mixed $class
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @return Model_Scheme
     */
    public static function getInstance($class, $postfix = null, $isRequired = false, $ttl = null)
    {
        return parent::getInstance($class, $postfix, $isRequired, $ttl);
    }

    public function getFieldColumnMap()
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getName();

        $repository = $modelClass::getRepository('mapping');
        $key = 'fieldColumnMap';

        return $repository->set($key, array_flip($this->getColumnFieldMap()));
    }

    public function getColumnFieldMap()
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getName();

        $repository = $modelClass::getRepository('mapping');
        $key = 'columnFieldMap';

        if ($columnFieldMapping = $repository->get($key)) {
            return $columnFieldMapping;
        }

        $columns = [];

        foreach ($this->gets('columns') as $columnName => $column) {
            $columns[$columnName] = $column['fieldName'];
        }

        return $repository->set($key, $columns);
    }

    /**
     * Return full field names
     *
     * @param  array $fields
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function getFieldNames($fields = [])
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getName();

        $fieldNames = array_values($this->getColumnFieldMap());

        if (empty($fields) || $fields = '*') {
            return $fieldNames;
        }

        $fields = (array)$fields;

        foreach ($fields as &$fieldName) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if (in_array($fieldName, $fieldNames)) {
                continue;
            }

            if (in_array($fieldName . '__json', $fieldNames)) {
                $fieldName = $fieldName . '__json';
                continue;
            }

            if (in_array($fieldName . '__fk', $fieldNames)) {
                $fieldName = $fieldName . '__fk';
                continue;
            }

            if (in_array($fieldName . '__geo', $fieldNames)) {
                $fieldName = $fieldName . '__geo';
                continue;
            }

            Logger::getInstance(self::getClass())->exception(
                ['Поле "{$0}" не найдено в модели "{$1}"', [$fieldName, self::getClass()]],
                __FILE__,
                __LINE__
            );
        }

        return $fields;
    }

    /**
     * Return all primary key names if them more then one
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function getPkFieldNames()
    {
        /**@var Model $modelClass */
        $modelClass = $this->getName();

        $repository = $modelClass::getRepository('scheme');
        $key = 'pkFieldNames';

        if ($pkFieldNames = $repository->get($key)) {
            return $pkFieldNames;
        }

        $columnFieldMappings = $this->getColumnFieldMap();

        return $repository->set(
            $key,
            array_map(
                function ($columnName) use ($columnFieldMappings) {
                    return $columnFieldMappings[$columnName];
                },
                $this->getPkColumnNames()
            )
        );
    }

    /**
     * Return primary key columns
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getPkColumnNames()
    {
        return $this->getIndexes()['PRIMARY KEY']['PRIMARY'];
    }

    /**
     * Return indexes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function getIndexes()
    {
        return $this->gets('indexes');
    }

    /**
     * Return unique key column names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    public function getUniqueColumnNames()
    {
        $uniqueColumnNames = [];

        foreach ($this->getIndexes()['UNIQUE'] as $SeqInIndex => $columnNames) {
            foreach ($columnNames as $columnName) {
                $uniqueColumnNames[] = $columnName;
            }
        }

        return $this->getIndexes()['PRIMARY KEY']['PRIMARY'];
    }

    /**
     * Return unique field names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    public function getUniqueFieldNames()
    {
        /**@var Model $modelClass */
        $modelClass = $this->getName();

        $repository = $modelClass::getRepository('scheme');
        $key = 'uniqueFieldNames';

        if ($uniqueFieldNames = $repository->get($key)) {
            return $uniqueFieldNames;
        }

        $columnFieldMappings = $this->getColumnFieldMap();

        return $repository->set(
            $key,
            array_map(
                function ($columnName) use ($columnFieldMappings) {
                    return $columnFieldMappings[$columnName];
                },
                $this->getUniqueColumnNames()
            )
        );
    }
}
