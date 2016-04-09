<?php

namespace Ice\Widget;

use Ice\Core\Data_Scheme;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Exception\Not_Configured;

abstract class Model_Table_Rows extends Table_Rows
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Table_Rows::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Not_Configured
     */
    protected function build(array $input)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getInstanceKey();

        if (!isset($input[$modelClass])) {
            throw new Not_Configured(['Check config of widget {$0} for {$1}', [get_class($this), $modelClass]]);
        }

        $this->setResource($modelClass);

        $currentTableName = $modelClass::getTableName();

        $currentDataSourceKey = $modelClass::getDataSourceKey();

        $currentSchemeName = 0;

        foreach (Module::getInstance()->getDataSourceKeys() as $index => $dataSourceKey) {
            if ($dataSourceKey == $currentDataSourceKey) {
                $currentSchemeName = $index;
                break;
            }
        }

        $scheme = Data_Scheme::getTables(Module::getInstance())[$currentDataSourceKey][$currentTableName];

        foreach ($scheme['columns'] as $column) {
            if (!isset($input[$modelClass][$column['fieldName']])) {
                continue;
            }

            $options = array_merge(
                ['label' => $column['scheme']['comment'] . ' (' . $column['fieldName'] . ')'],
                $input[$modelClass][$column['fieldName']]
            );

            $fieldType = isset($input[$modelClass][$column['fieldName']]['type'])
                ? $input[$modelClass][$column['fieldName']]['type']
                : 'span';

            $this->$fieldType(
                $column['fieldName'],
                array_merge(
                    ['params' => ['schemeName' => $currentSchemeName, 'tableName' => $currentTableName]],
                    $options
                )
            );
        }
    }
}
