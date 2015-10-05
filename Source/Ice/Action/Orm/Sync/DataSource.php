<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Source;
use Ice\Core\Data_Scheme;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Exception\DataSource_TableNotFound;
use Ice\Helper\Json;
use Ice\Model\Scheme;

class Orm_Sync_DataSource extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'actions' => [],
            'input' => ['force' => ['default' => 0]],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'roles' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $module = Module::getInstance();

        $dataSourceTables = $module->getDataSourceTables();

        foreach (Data_Scheme::getTables($module) as $dataSourceKey => $tables) {
            try {
                $schemes = Scheme::createQueryBuilder()->getSelectQuery('*', [], $dataSourceKey)->getRows();
            } catch (DataSource_TableNotFound $e) {
                Scheme::createTable($dataSourceKey);
                $schemes = [];
            }

            $sourceTables = &$dataSourceTables[$dataSourceKey];

            foreach ($tables as $tableName => $table) {
                if (!isset($sourceTables[$tableName])) {
                    if (!array_key_exists($tableName, $schemes)) {
                        $this->createTable($table['modelClass'], $table, $dataSourceKey);
                    }

                    continue;
                }

//                $updated = false;
//
//                $tableSourceHash = &$sourceTables[$tableName]['sourceHash'];
//                $tableSource = &$sourceTables[$tableName]['source'];
//
//                if ($table['sourceHash'] != $tableSourceHash) {
//                    Data_Source::getLogger()->info([
//                        'Update source for model {$0}: {$1}',
//                        [
//                            $sourceTables[$tableName]['modelClass'],
//                            Json::encode(array_diff($table['source'], $tableSource))
//                        ]
//                    ]);
//                    $tableSource = $table['source'];
//                    $tableSourceHash = $table['sourceHash'];
//                    $updated = true;
//                }
//
//                $tableIndexesHash = &$sourceTables[$tableName]['indexesHash'];
//                $tableIndexes = &$sourceTables[$tableName]['indexes'];
//
//                if ($table['indexesHash'] != $tableIndexesHash) {
//                    Data_Source::getLogger()->info([
//                        'Update indexes for model {$0}: {$1}',
//                        [$sourceTables[$tableName]['modelClass'], Json::encode($table['indexes'])]
//                    ]);
//                    $tableIndexes = $table['indexes'];
//                    $tableIndexesHash = $table['indexesHash'];
//                    $updated = true;
//                }
//
//
//                if (!isset($sourceTables[$tableName]['referencesHash'])) {
//                    $sourceTables[$tableName]['referencesHash'] = crc32(Json::encode([]));
//                }
//                if (!isset($table['references'])) {
//                    $table['references'] = [];
//                }
//                if (!isset($table['referencesHash'])) {
//                    $table['referencesHash'] = '';
//                }
//                if ($table['referencesHash'] != $sourceTables[$tableName]['referencesHash']) {
//                    Data_Source::getLogger()->info([
//                        'Update references for model {$0}: {$1}',
//                        [$sourceTables[$tableName]['modelClass'], Json::encode($table['references'])]
//                    ]);
//                    $sourceTables[$tableName]['references'] = $table['references'];
//                    $sourceTables[$tableName]['referencesHash'] = $table['referencesHash'];
//                    $updated = true;
//                }
//
//                if (!isset($sourceTables[$tableName]['oneToManyHash'])) {
//                    $sourceTables[$tableName]['oneToManyHash'] = crc32(Json::encode([]));
//                }
//                if (!isset($table['oneToMany'])) {
//                    $table['oneToMany'] = [];
//                }
//                $table['oneToManyHash'] = crc32(Json::encode($table['oneToMany']));
//                if ($input['force'] || $table['oneToManyHash'] != $sourceTables[$tableName]['oneToManyHash']) {
//                    $references = [];
//                    foreach ($table['oneToMany'] as $referenceTableName => $columnName) {
//                        $referenceClassName = isset($sourceTables[$referenceTableName])
//                            ? $sourceTables[$referenceTableName]['modelClass']
//                            : $module->getModelClass($referenceTableName, $dataSourceKey);
//
//                        $references[$referenceClassName] = $columnName;
//                    }
//                    $table['oneToMany'] = $references;
//                    Data_Source::getLogger()->info([
//                        'Update OneToMany references for model {$0}: {$1}',
//                        [$sourceTables[$tableName]['modelClass'], Json::encode($table['oneToMany'])]
//                    ]);
//                    $sourceTables[$tableName]['oneToMany'] = $table['oneToMany'];
//                    $sourceTables[$tableName]['oneToManyHash'] = $table['oneToManyHash'];
//                    $updated = true;
//                }
//
//                if (!isset($sourceTables[$tableName]['manyToOneHash'])) {
//                    $sourceTables[$tableName]['manyToOneHash'] = crc32(Json::encode([]));
//                }
//                if (!isset($table['manyToOne'])) {
//                    $table['manyToOne'] = [];
//                }
//                $table['manyToOneHash'] = crc32(Json::encode($table['manyToOne']));
//                if ($input['force'] || $table['manyToOneHash'] != $sourceTables[$tableName]['manyToOneHash']) {
//                    $references = [];
//                    foreach ($table['manyToOne'] as $referenceTableName => $columnName) {
//                        $referenceClassName = isset($sourceTables[$referenceTableName])
//                            ? $sourceTables[$referenceTableName]['modelClass']
//                            : $module->getModelClass($referenceTableName, $dataSourceKey);
//
//                        $references[$referenceClassName] = $columnName;
//                    }
//                    $table['manyToOne'] = $references;
//                    Data_Source::getLogger()->info([
//                        'Update ManyToOne references for model {$0}: {$1}',
//                        [$sourceTables[$tableName]['modelClass'], Json::encode($table['manyToOne'])]
//                    ]);
//                    $sourceTables[$tableName]['manyToOne'] = $table['manyToOne'];
//                    $sourceTables[$tableName]['manyToOneHash'] = $table['manyToOneHash'];
//                    $updated = true;
//                }
//
//                if (!isset($sourceTables[$tableName]['manyToManyHash'])) {
//                    $sourceTables[$tableName]['manyToManyHash'] = crc32(Json::encode([]));
//                }
//                if (!isset($table['manyToMany'])) {
//                    $table['manyToMany'] = [];
//                }
//                $table['manyToManyHash'] = crc32(Json::encode($table['manyToMany']));
//                if ($input['force'] || $table['manyToManyHash'] != $sourceTables[$tableName]['manyToManyHash']) {
//                    $references = [];
//                    foreach ($table['manyToMany'] as $referenceTableName => $linkTableName) {
//                        $referenceClassName = isset($sourceTables[$referenceTableName])
//                            ? $sourceTables[$referenceTableName]['modelClass']
//                            : $module->getModelClass($referenceTableName, $dataSourceKey);
//
//                        $linkClassName = isset($sourceTables[$linkTableName])
//                            ? $sourceTables[$linkTableName]['modelClass']
//                            : $module->getModelClass($linkTableName, $dataSourceKey);
//
//                        $references[$referenceClassName] = $linkClassName;
//                    }
//                    $table['manyToMany'] = $references;
//                    Data_Source::getLogger()->info([
//                        'Update ManyToMany references for model {$0}: {$1}',
//                        [$sourceTables[$tableName]['modelClass'], Json::encode($table['manyToMany'])]
//                    ]);
//                    $sourceTables[$tableName]['manyToMany'] = $table['manyToMany'];
//                    $sourceTables[$tableName]['manyToManyHash'] = $table['manyToManyHash'];
//                    $updated = true;
//                }
//
//                $dataSourceColumns = $sourceTables[$tableName]['columns'];
//
//                foreach ($table['columns'] as $columnName => $column) {
//                    if (!isset($sourceTables[$tableName]['columns'][$columnName])) {
//                        $sourceTables[$tableName]['columns'][$columnName] = [
//                            'source' => $column['source'],
//                            'sourceHash' => $column['sourceHash']
//                        ];
//                        Data_Source::getLogger()->info([
//                            'Create field {$0} for model {$1}',
//                            [$column['fieldName'], $sourceTables[$tableName]['modelClass']]
//                        ]);
//                        $updated = true;
//                        continue;
//                    }
//
//                    $columnSourceHash = &$sourceTables[$tableName]['columns'][$columnName]['sourceHash'];
//                    $columnSource = &$sourceTables[$tableName]['columns'][$columnName]['source'];
//
//                    if ($column['sourceHash'] != $columnSourceHash) {
//                        Data_Source::getLogger()->info([
//                            'Update field {$0} for model {$1}: {$2}',
//                            [
//                                $column['fieldName'],
//                                $sourceTables[$tableName]['modelClass'],
//                                Json::encode(array_diff($column['source'], $columnSource))
//                            ]
//                        ]);
//                        $columnSource = $column['source'];
//                        $columnSourceHash = $column['sourceHash'];
//                        $updated = true;
//                    }
//
//                    unset($dataSourceColumns[$columnName]);
//                }
//
//                foreach ($dataSourceColumns as $columnName => $column) {
//                    Data_Source::getLogger()->info([
//                        'Remove field {$0} for model {$1}',
//                        [$column['fieldName'], $sourceTables[$tableName]['modelClass']]
//                    ]);
//                    unset($sourceTables[$tableName]['columns'][$columnName]);
//                    $updated = true;
//                }
//
//                if ($updated) {
//                    Model::getCodeGenerator($sourceTables[$tableName]['modelClass'])->generate($table, $input['force']);
//                }
//
//                unset($sourceTables[$tableName]);
            }
        }

//        foreach ($dataSourceTables as $dataSourceKey => $sourceTables) {
//            foreach ($sourceTables as $tableName => $table) {
//                if (array_key_exists($tableName, $schemes)) {
//                    $this->deleteModel(
//                        $module->get(Module::Scheme_DIR) . $table['modelPath'],
//                        $tableName,
//                        $sourceTables);
//                }
//            }
//        }
    }

    /**
     * @param Model $modelClass
     * @param $dataSourceKey
     * @param $table
     */
    private function createTable($modelClass, $table, $dataSourceKey)
    {

        $modelClass::createTable($dataSourceKey);

        Scheme::createQueryBuilder()->getInsertQuery(
            [
                'table_name' => $table['scheme']['tableName'],
                'table__json' => Json::encode($table['scheme']),
                'columns__json' => Json::encode($table['columns']),
                'indexes__json' => Json::encode($table['indexes']),
                'references__json' => Json::encode($table['references']),
                'revision' => $table['revision']
            ],
            true,
            $dataSourceKey
        )->getQueryResult();

        Data_Source::getLogger()->info(
            ['{$0}: Table {$1} successfully created', [$dataSourceKey, $table['scheme']['tableName']]]
        );
    }

    private function deleteModel($modelFilePath, $tableName, $sourceTables, $dataSourceKey)
    {

        if (file_exists($modelFilePath)) {
            unlink($modelFilePath);
        }

        Scheme::createQueryBuilder()->deleteQuery($tableName, $dataSourceKey)->getQueryResult();

        Data_Source::getLogger()->info(['Model {$0} deleted', $sourceTables[$tableName]['modelClass']]);
    }
}