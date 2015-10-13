<?php

namespace Ice\Widget;

use Ice\Core\Config;
use Ice\Core\Data_Scheme;
use Ice\Core\Model;
use Ice\Core\Module;

class Model_Table_Rows extends Table_Rows
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
            'input' => ['config' => ['validators' => 'Ice:Not_Empty']],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getInstanceKey();

        $pkFieldName = $modelClass::getPkFieldName();

        $currentTableName = $modelClass::getTableName();

        $currentDataSourceKey = $modelClass::getDataSourceKey();

        $schemeName = 0;

        foreach (Module::getInstance()->getDataSourceKeys() as $index => $dataSourceKey) {
            if ($dataSourceKey == $currentDataSourceKey) {
                $schemeName = $index;
                break;
            }
        }

        $config = Config::getInstance($input['config'])->getConfig($modelClass);

        foreach (Data_Scheme::getTables(Module::getInstance()) as $dataSourceKey => $tables) {
            if ($dataSourceKey == $currentDataSourceKey) {
                foreach ($tables as $tableName => $table) {
                    if ($tableName == $currentTableName) {
                        foreach ($table['columns'] as $field) {
                            $params = $config->gets($field['fieldName'], false);

                            if ($field['fieldName'] == $pkFieldName) {
                                $this->a(
                                    $field['fieldName'],
                                    array_merge(
                                        ['params' => ['schemeName' => $schemeName, 'tableName' => $currentTableName]] ,
                                        $params
                                    )
                                );
                            } else {
                                $this->span(
                                    $field['fieldName'],
                                    array_merge(
                                        ['label' => $field['scheme']['comment'] . ' (' . $field['fieldName'] . ')'],
                                        $params
                                    )
                                );
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
        }
    }
}