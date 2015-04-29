<?php

namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Data_Source;
use Ice\Core\Model;
use Ice\Core\Module;
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
            'ttl' => -1,
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
        $schemes = Scheme::createQueryBuilder()->getSelectQuery('*')->getRows();

        $module = Module::getInstance();

        $dataSchemeTables = Data_Scheme::getTables($module);

        foreach ($module->getDataSourceTables() as $dataSourceKey => $tables) {
            Data_Scheme::getLogger()->info(['Checking models from data source {$0}', $dataSourceKey]);

            $schemeTables = &$dataSchemeTables[$dataSourceKey];

            foreach ($tables as $tableName => $table) {
                if (!isset($schemeTables[$tableName])) {
                    if (!array_key_exists($tableName, $schemes)) {
                        $this->createModel($module->getModelClass($tableName, $dataSourceKey), $table, $input['force']);
                    }

                    continue;
                }

                $updated = false;

                $tableSchemeHash = &$schemeTables[$tableName]['schemeHash'];
                $tableScheme = &$schemeTables[$tableName]['scheme'];

                if ($table['schemeHash'] != $tableSchemeHash) {
                    Data_Scheme::getLogger()->info([
                        'Update scheme for model {$0}: {$1}',
                        [
                            $schemeTables[$tableName]['modelClass'],
                            Json::encode(array_diff($table['scheme'], $tableScheme))
                        ]
                    ]);
                    $tableScheme = $table['scheme'];
                    $tableSchemeHash = $table['schemeHash'];
                    $updated = true;
                }

                $tableIndexesHash = &$schemeTables[$tableName]['indexesHash'];
                $tableIndexes = &$schemeTables[$tableName]['indexes'];

                if ($table['indexesHash'] != $tableIndexesHash) {
                    Data_Scheme::getLogger()->info([
                        'Update indexes for model {$0}: {$1}',
                        [$schemeTables[$tableName]['modelClass'], Json::encode($table['indexes'])]
                    ]);
                    $tableIndexes = $table['indexes'];
                    $tableIndexesHash = $table['indexesHash'];
                    $updated = true;
                }


                if (!isset($schemeTables[$tableName]['referencesHash'])) {
                    $schemeTables[$tableName]['referencesHash'] = crc32(Json::encode([]));
                }
                if (!isset($table['references'])) {
                    $table['references'] = [];
                }
                if (!isset($table['referencesHash'])) {
                    $table['referencesHash'] = '';
                }
                if ($table['referencesHash'] != $schemeTables[$tableName]['referencesHash']) {
                    Data_Scheme::getLogger()->info([
                        'Update references for model {$0}: {$1}',
                        [$schemeTables[$tableName]['modelClass'], Json::encode($table['references'])]
                    ]);
                    $schemeTables[$tableName]['references'] = $table['references'];
                    $schemeTables[$tableName]['referencesHash'] = $table['referencesHash'];
                    $updated = true;
                }

                if (!isset($schemeTables[$tableName]['oneToManyHash'])) {
                    $schemeTables[$tableName]['oneToManyHash'] = crc32(Json::encode([]));
                }
                if (!isset($table['oneToMany'])) {
                    $table['oneToMany'] = [];
                }
                $table['oneToManyHash'] = crc32(Json::encode($table['oneToMany']));
                if ($input['force'] || $table['oneToManyHash'] != $schemeTables[$tableName]['oneToManyHash']) {
                    $references = [];
                    foreach ($table['oneToMany'] as $referenceTableName => $columnName) {
                        $referenceClassName = isset($schemeTables[$referenceTableName])
                            ? $schemeTables[$referenceTableName]['modelClass']
                            : $module->getModelClass($referenceTableName, $dataSourceKey);

                        $references[$referenceClassName] = $columnName;
                    }
                    $table['oneToMany'] = $references;
                    Data_Scheme::getLogger()->info([
                        'Update OneToMany references for model {$0}: {$1}',
                        [$schemeTables[$tableName]['modelClass'], Json::encode($table['oneToMany'])]
                    ]);
                    $schemeTables[$tableName]['oneToMany'] = $table['oneToMany'];
                    $schemeTables[$tableName]['oneToManyHash'] = $table['oneToManyHash'];
                    $updated = true;
                }

                if (!isset($schemeTables[$tableName]['manyToOneHash'])) {
                    $schemeTables[$tableName]['manyToOneHash'] = crc32(Json::encode([]));
                }
                if (!isset($table['manyToOne'])) {
                    $table['manyToOne'] = [];
                }
                $table['manyToOneHash'] = crc32(Json::encode($table['manyToOne']));
                if ($input['force'] || $table['manyToOneHash'] != $schemeTables[$tableName]['manyToOneHash']) {
                    $references = [];
                    foreach ($table['manyToOne'] as $referenceTableName => $columnName) {
                        $referenceClassName = isset($schemeTables[$referenceTableName])
                            ? $schemeTables[$referenceTableName]['modelClass']
                            : $module->getModelClass($referenceTableName, $dataSourceKey);

                        $references[$referenceClassName] = $columnName;
                    }
                    $table['manyToOne'] = $references;
                    Data_Scheme::getLogger()->info([
                        'Update ManyToOne references for model {$0}: {$1}',
                        [$schemeTables[$tableName]['modelClass'], Json::encode($table['manyToOne'])]
                    ]);
                    $schemeTables[$tableName]['manyToOne'] = $table['manyToOne'];
                    $schemeTables[$tableName]['manyToOneHash'] = $table['manyToOneHash'];
                    $updated = true;
                }

                if (!isset($schemeTables[$tableName]['manyToManyHash'])) {
                    $schemeTables[$tableName]['manyToManyHash'] = crc32(Json::encode([]));
                }
                if (!isset($table['manyToMany'])) {
                    $table['manyToMany'] = [];
                }
                $table['manyToManyHash'] = crc32(Json::encode($table['manyToMany']));
                if ($input['force'] || $table['manyToManyHash'] != $schemeTables[$tableName]['manyToManyHash']) {
                    $references = [];
                    foreach ($table['manyToMany'] as $referenceTableName => $linkTableName) {
                        $referenceClassName = isset($schemeTables[$referenceTableName])
                            ? $schemeTables[$referenceTableName]['modelClass']
                            : $module->getModelClass($referenceTableName, $dataSourceKey);

                        $linkClassName = isset($schemeTables[$linkTableName])
                            ? $schemeTables[$linkTableName]['modelClass']
                            : $module->getModelClass($linkTableName, $dataSourceKey);

                        $references[$referenceClassName] = $linkClassName;
                    }
                    $table['manyToMany'] = $references;
                    Data_Scheme::getLogger()->info([
                        'Update ManyToMany references for model {$0}: {$1}',
                        [$schemeTables[$tableName]['modelClass'], Json::encode($table['manyToMany'])]
                    ]);
                    $schemeTables[$tableName]['manyToMany'] = $table['manyToMany'];
                    $schemeTables[$tableName]['manyToManyHash'] = $table['manyToManyHash'];
                    $updated = true;
                }

                $dataSchemeColumns = $schemeTables[$tableName]['columns'];

                foreach ($table['columns'] as $columnName => $column) {
                    if (!isset($schemeTables[$tableName]['columns'][$columnName])) {
                        $schemeTables[$tableName]['columns'][$columnName] = [
                            'scheme' => $column['scheme'],
                            'schemeHash' => $column['schemeHash']
                        ];
                        Data_Scheme::getLogger()->info([
                            'Create field {$0} for model {$1}',
                            [$column['fieldName'], $schemeTables[$tableName]['modelClass']]
                        ]);
                        $updated = true;
                        continue;
                    }

                    $columnSchemeHash = &$schemeTables[$tableName]['columns'][$columnName]['schemeHash'];
                    $columnScheme = &$schemeTables[$tableName]['columns'][$columnName]['scheme'];

                    if ($column['schemeHash'] != $columnSchemeHash) {
                        Data_Scheme::getLogger()->info([
                            'Update field {$0} for model {$1}: {$2}',
                            [
                                $column['fieldName'],
                                $schemeTables[$tableName]['modelClass'],
                                Json::encode(array_diff($column['scheme'], $columnScheme))
                            ]
                        ]);
                        $columnScheme = $column['scheme'];
                        $columnSchemeHash = $column['schemeHash'];
                        $updated = true;
                    }

                    unset($dataSchemeColumns[$columnName]);
                }

                foreach ($dataSchemeColumns as $columnName => $column) {
                    Data_Scheme::getLogger()->info([
                        'Remove field {$0} for model {$1}',
                        [$column['fieldName'], $schemeTables[$tableName]['modelClass']]
                    ]);
                    unset($schemeTables[$tableName]['columns'][$columnName]);
                    $updated = true;
                }

                if ($updated) {
                    Model::getCodeGenerator()->generate($schemeTables[$tableName]['modelClass'], $table, $input['force']);
                }

                unset($schemeTables[$tableName]);
            }
        }

        foreach ($dataSchemeTables as $dataSourceKey => $schemeTables) {
            foreach ($schemeTables as $tableName => $table) {
                if (array_key_exists($tableName, $schemes)) {
                    $this->deleteModel(
                        $module->get(Module::SOURCE_DIR) . $table['modelPath'],
                        $tableName,
                        $schemeTables);
                }
            }
        }
    }

    private function createModel($modelClass, $table, $force)
    {
        Model::getCodeGenerator()->generate($modelClass, $table, $force);

        Scheme::createQueryBuilder()->insertQuery([
            'table_name' => $table['scheme']['tableName'],
            'table__json' => Json::encode($table['scheme']),
            'columns__json' => Json::encode($table['columns']),
            'indexes__json' => Json::encode($table['indexes']),
            'references__json' => Json::encode($table['references']),
            'revision' => $table['revision']
        ], true)->getQueryResult();

        Data_Scheme::getLogger()->info(['Model {$0} created', $modelClass]);
    }

    private function deleteModel($modelFilePath, $tableName, $schemeTables) {

        if (file_exists($modelFilePath)) {
            unlink($modelFilePath);
        }

        Scheme::createQueryBuilder()->deleteQuery($tableName)->getQueryResult();

        Data_Scheme::getLogger()->info(['Model {$0} deleted', $schemeTables[$tableName]['modelClass']]);
    }
}