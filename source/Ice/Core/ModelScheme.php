<?php

namespace Ice\Core;

use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;

class ModelScheme extends Config
{
    const ONE_TO_MANY = 'oneToMany';
    const MANY_TO_ONE = 'manyToOne';
    const MANY_TO_MANY = 'manyToMany';

    /**
     * @param mixed $class
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @param array $config
     * @return ModelScheme|Config
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public static function getInstance($class, $postfix = null, $isRequired = false, $ttl = null, array $config = [])
    {
        return parent::getInstance($class, $postfix, $isRequired, $ttl, $config);
    }

    public function getFieldColumnMap()
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getName();

        $repository = $modelClass::getRepository('mapping');
        $key = 'fieldColumnMap';

        return $repository->set([$key => array_flip($this->getColumnFieldMap())])[$key];
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
            if (reset($column) === null) {
                continue;
            }

            $columns[$columnName] = $column['fieldName'];
        }

        return $repository->set([$key => $columns])[$key];
    }

    /**
     * Return full field names
     *
     * @param array $fields
     * @return array
     *
     * @throws Exception
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

        if (empty($fields) || $fields === '*') {
            return $fieldNames;
        }

        $fields = (array)$fields;

        foreach ($fields as &$fieldName) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if (in_array($fieldName, $fieldNames, true)) {
                continue;
            }

            if (in_array($fieldName . '__json', $fieldNames, true)) {
                $fieldName .= '__json';
                continue;
            }

            if (in_array($fieldName . '__fk', $fieldNames, true)) {
                $fieldName .= '__fk';
                continue;
            }

            if (in_array($fieldName . '__geo', $fieldNames, true)) {
                $fieldName .= '__geo';
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

        return $repository->set([
            $key => array_map(
                function ($columnName) use ($columnFieldMappings) {
                    return $columnFieldMappings[$columnName];
                },
                $this->getPkColumnNames()
            )
        ])[$key];
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
     * Return unique field names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @deprecated need refactor with use ::getUniqueIndexes()
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

        return $repository->set([
            $key => array_map(
                function ($columnName) use ($columnFieldMappings) {
                    return $columnFieldMappings[$columnName];
                },
                $this->getUniqueColumnNames()
            )
        ])[$key];
    }

    /**
     * Return unique key column names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @deprecated use ::getUniqueIndexes()
     */
    public function getUniqueColumnNames()
    {
        $uniqueColumnNames = [];

        foreach ($this->getUniqueIndexes() as $columnNames) {
            foreach ($columnNames as $columnName) {
                $uniqueColumnNames[] = $columnName;
            }
        }

        return $uniqueColumnNames;
    }

    public function getUniqueIndexes()
    {
        return array_merge($this->getIndexes()['PRIMARY KEY'], $this->getIndexes()['UNIQUE']);
    }
}
