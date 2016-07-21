<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\DataProvider\Cli;
use Ice\Exception\DataSource_Statement_TableNotFound;
use Ice\Helper\Arrays;
use Ice\Helper\Json;
use Ice\Model\Scheme;

class Orm_Sync_DataScheme extends Action
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
            'view' => ['template' => '', 'viewRenderClass' => null],
            'actions' => [],
            'input' => [
                'force' => ['providers' => Cli::class, 'default' => 0],
                'updatePlugins' => ['providers' => Cli::class, 'default' => 0]
            ],
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

        $dataSchemeTables = Data_Scheme::getTables($module);

        foreach ($module->getDataSourceTables() as $dataSourceKey => $tables) {
            try {
                $schemes = Scheme::createQueryBuilder()->getSelectQuery('*', [], $dataSourceKey)->getRows();
            } catch (DataSource_Statement_TableNotFound $e) {
                Scheme::createTable($dataSourceKey);
                $schemes = [];
            }

            $schemeTables = &$dataSchemeTables[$dataSourceKey];

            foreach ($tables as $tableName => $table) {
                $relations = [];
                foreach ($table['relations']['oneToMany'] as $referenceTableName => $columnName) {
                    $referenceClassName = isset($schemeTables[$referenceTableName])
                        ? $schemeTables[$referenceTableName]['modelClass']
                        : $module->getModelClass($referenceTableName, $dataSourceKey);

                    $relations[$referenceClassName] = $columnName;
                }
                $table['relations']['oneToMany'] = $relations;

                $relations = [];
                foreach ($table['relations']['manyToOne'] as $referenceTableName => $columnName) {
                    $referenceClassName = isset($schemeTables[$referenceTableName])
                        ? $schemeTables[$referenceTableName]['modelClass']
                        : $module->getModelClass($referenceTableName, $dataSourceKey);

                    $relations[$referenceClassName] = $columnName;
                }
                $table['relations']['manyToOne'] = $relations;

                $relations = [];
                foreach ($table['relations']['manyToMany'] as $referenceTableName => $linkTableNames) {
                    $referenceClassName = isset($schemeTables[$referenceTableName])
                        ? $schemeTables[$referenceTableName]['modelClass']
                        : $module->getModelClass($referenceTableName, $dataSourceKey);

                    $relations[$referenceClassName] = [];

                    foreach ($linkTableNames as $linkTableName) {
                        $linkClassName = isset($schemeTables[$linkTableName])
                            ? $schemeTables[$linkTableName]['modelClass']
                            : $module->getModelClass($linkTableName, $dataSourceKey);

                        $relations[$referenceClassName][] = $linkClassName;
                    }
                }
                $table['relations']['manyToMany'] = $relations;

                if (!isset($schemeTables[$tableName])) {
                    if (!array_key_exists($tableName, $schemes)) {
                        $this->createModel(
                            $module->getModelClass($tableName, $dataSourceKey),
                            $table,
                            $input['force'],
                            $dataSourceKey
                        );
                    }

                    continue;
                }

                $isModelSchemeUpdated = $this->updateModelScheme(
                    $table['scheme'],
                    $schemeTables[$tableName]['scheme'],
                    $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelIndexesUpdated = $this->updateModelIndexes(
                    $table['indexes'],
                    $schemeTables[$tableName]['indexes'],
                    $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelReferencesUpdated = $this->updateModelReferences(
                    $table['references'],
                    $schemeTables[$tableName]['references'],
                    $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelRelationsOneToManyUpdated = $this->updateModelRelationsOneToMany(
                    $table['relations']['oneToMany'],
                    $schemeTables[$tableName]['relations']['oneToMany'],
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey,
                    $input['force']
                );

                $isModelRelationsManyToOneUpdated = $this->updateModelRelationsManyToOne(
                    $table['relations']['manyToOne'],
                    $schemeTables[$tableName]['relations']['manyToOne'],
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey,
                    $input['force']
                );

                $isModelRelationsManyToManyUpdated = $this->updateModelRelationsManyToMany(
                    $table['relations']['manyToMany'],
                    $schemeTables[$tableName]['relations']['manyToMany'],
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey,
                    $input['force']
                );

                $isModelFieldsUpdated = false;

                $dataSchemeColumns = $schemeTables[$tableName]['columns'];

                foreach ($table['columns'] as $columnName => $column) {
                    if (!isset($schemeTables[$tableName]['columns'][$columnName])) {
                        $this->createModuleField(
                            $schemeTables[$tableName]['columns'][$columnName],
                            $column,
                            $schemeTables[$tableName]['modelClass'],
                            $dataSourceKey
                        );
                        $isModelFieldsUpdated = true;
                        continue;
                    }

                    $isModelFieldUpdated = $this->updateModelField(
                        $column['scheme'],
                        $schemeTables[$tableName]['columns'][$columnName]['scheme'],
                        $schemeTables[$tableName]['columns'][$columnName]['fieldName'],
                        $schemeTables[$tableName]['modelClass'],
                        $dataSourceKey
                    );

                    if ($input['updatePlugins']) {
                        $isModelFieldUpdated = true;

                        if (!isset($schemeTables[$tableName]['columns'][$columnName]['options'])) {
                            $schemeTables[$tableName]['columns'][$columnName]['options'] = [
                                'name' => $schemeTables[$tableName]['columns'][$columnName]['fieldName']
                            ];
                        }

                        $schemeTables[$tableName]['columns'][$columnName]['options'] = array_merge(
                            $column['options'],
                            $schemeTables[$tableName]['columns'][$columnName]['options']
                        );
                    }

                    if (!$isModelFieldsUpdated) {
                        $isModelFieldsUpdated = $isModelFieldUpdated;
                    }

                    unset($dataSchemeColumns[$columnName]);
                }

                foreach ($dataSchemeColumns as $columnName => $column) {
                    $this->getLogger()->info([
                        'Remove field {$0} for model {$1}',
                        [$column['fieldName'], $schemeTables[$tableName]['modelClass']],
                        Logger::INFO,
                        true
                    ]);
                    unset($schemeTables[$tableName]['columns'][$columnName]);
                    $isModelFieldsUpdated = true;
                }

                if ($isModelFieldsUpdated) {
                    Scheme::createQueryBuilder()
                        ->pk($tableName)
                        ->getUpdateQuery(['columns__json' => Json::encode($table['columns'])], $dataSourceKey)
                        ->getQueryResult();
                }

                $isUpdated = $isModelSchemeUpdated ||
                    $isModelIndexesUpdated ||
                    $isModelReferencesUpdated ||
                    $isModelRelationsOneToManyUpdated ||
                    $isModelRelationsManyToOneUpdated ||
                    $isModelRelationsManyToManyUpdated ||
                    $isModelFieldsUpdated;

                if ($isUpdated) {
                    Model::getCodeGenerator($schemeTables[$tableName]['modelClass'])
                        ->generate($schemeTables[$tableName], $input['force']);
                }

                unset($schemeTables[$tableName]);
            }
        }

        foreach ($dataSchemeTables as $dataSourceKey => $schemeTables) {
            $schemes = Scheme::createQueryBuilder()->getSelectQuery('*', [], $dataSourceKey)->getRows();

            foreach ($schemeTables as $tableName => $table) {
                if (array_key_exists($tableName, $schemes)) {
                    $this->deleteModel(
                        $module->getPath(Module::SOURCE_DIR) . $table['modelPath'],
                        $tableName,
                        $schemeTables,
                        $dataSourceKey
                    );
                }
            }
        }
    }

    private function createModel($modelClass, $table, $force, $dataSourceKey)
    {
        Model::getCodeGenerator($modelClass)->generate($table, $force);

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

        $this->getLogger()->info(
            ['{$0}: Model {$1} successfully created', [$dataSourceKey, $modelClass]],
            Logger::INFO,
            true
        );
    }

    private function deleteModel($modelFilePath, $tableName, $schemeTables, $dataSourceKey)
    {
        if (file_exists($modelFilePath)) {
            unlink($modelFilePath);
        }

        Scheme::createQueryBuilder()->getDeleteQuery($tableName, $dataSourceKey)->getQueryResult();

        $this->getLogger()->info(
            ['{$0}: Model {$1} successfully deleted', [$dataSourceKey, $schemeTables[$tableName]['modelClass']]],
            Logger::INFO,
            true
        );
    }

    private function updateModelScheme(array $tableScheme, array &$modelScheme, $tableName, $modelClass, $dataSourceKey)
    {
        $tableSchemeJson = Json::encode($tableScheme);

        if (crc32($tableSchemeJson) == crc32(Json::encode($modelScheme))) {
            return false;
        }

        $diffScheme = Json::encode(array_diff($tableScheme, $modelScheme));

        $modelScheme = $tableScheme;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->getUpdateQuery(['table__json' => $tableSchemeJson], $dataSourceKey)
            ->getQueryResult();

        $this->getLogger()->info([
            '{$0}: Scheme of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffScheme],
            Logger::INFO,
            true
        ]);

        return true;
    }

    private function updateModelIndexes(
        array $tableIndexes,
        array &$modelIndexes,
        $tableName,
        $modelClass,
        $dataSourceKey
    )
    {
        $tableIndexesJson = Json::encode($tableIndexes);

        if (crc32($tableIndexesJson) == crc32(Json::encode($modelIndexes))) {
            return false;
        }

        $addedDiffIndexes = Json::encode(Arrays::diffRecursive($tableIndexes, $modelIndexes));
        $removedDiffIndexes = Json::encode(Arrays::diffRecursive($tableIndexes, $tableIndexes));

        $modelIndexes = $tableIndexes;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->getUpdateQuery(['indexes__json' => $tableIndexesJson], $dataSourceKey)
            ->getQueryResult();

        $this->getLogger()->info([
            '{$0}: Indexes of model {$1} successfully updated! [added: {$2}; removed: {$3}]',
            [$dataSourceKey, $modelClass, $addedDiffIndexes, $removedDiffIndexes],
            Logger::INFO,
            true
        ]);

        return true;
    }

    private function updateModelReferences(
        array $tableReferences,
        array &$modelReferences,
        $tableName,
        $modelClass,
        $dataSourceKey
    )
    {
        $tableReferencesJson = Json::encode($tableReferences);

        if (crc32($tableReferencesJson) == crc32(Json::encode($modelReferences))) {
            return false;
        }

        $addedDiffReferences = Json::encode(Arrays::diffRecursive($tableReferences, $modelReferences));
        $removedDiffReferences = Json::encode(Arrays::diffRecursive($modelReferences, $tableReferences));

        $modelReferences = $tableReferences;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->getUpdateQuery(['references__json' => $tableReferencesJson], $dataSourceKey)
            ->getQueryResult();

        $this->getLogger()->info([
            '{$0}: References of model {$1} successfully updated! [added: {$2}; removed: {$3}]',
            [$dataSourceKey, $modelClass, $addedDiffReferences, $removedDiffReferences]
        ]);

        return true;
    }

    private function updateModelRelationsOneToMany(
        array $tableOneToMany,
        array &$modelOneToMany,
        $modelClass,
        $dataSourceKey,
        $force
    )
    {
        $tableOneToManyJson = Json::encode($tableOneToMany);

        if (!$force && crc32($tableOneToManyJson) == crc32(Json::encode($modelOneToMany))) {
            return false;
        }

        $diffOneToMany = Json::encode(array_diff($tableOneToMany, $modelOneToMany));

        $modelOneToMany = $tableOneToMany;

        $this->getLogger()->info([
            '{$0}: OneToMany relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffOneToMany],
            Logger::INFO,
            true
        ]);

        return true;
    }

    private function updateModelRelationsManyToOne(
        array $tableManyToOne,
        array &$modelManyToOne,
        $modelClass,
        $dataSourceKey,
        $force
    )
    {
        $tableManyToOneJson = Json::encode($tableManyToOne);

        if (!$force && crc32($tableManyToOneJson) == crc32(Json::encode($modelManyToOne))) {
            return false;
        }

        $diffManyToOne = Json::encode(array_diff($tableManyToOne, $modelManyToOne));

        $modelManyToOne = $tableManyToOne;

        $this->getLogger()->info([
            '{$0}: ManyToOne relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffManyToOne],
            Logger::INFO,
            true
        ]);

        return true;
    }

    private function updateModelRelationsManyToMany(
        array $tableManyToMany,
        array &$modelManyToMany,
        $modelClass,
        $dataSourceKey,
        $force
    )
    {
        $tableManyToOneJson = Json::encode($tableManyToMany);

        if (!$force && crc32($tableManyToOneJson) == crc32(Json::encode($modelManyToMany))) {
            return false;
        }

        $diffManyToMany = '';//Json::encode(array_diff($tableManyToMany, $modelManyToMany));

        $modelManyToMany = $tableManyToMany;

        $this->getLogger()->info([
            '{$0}: ManyToMany relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffManyToMany],
            Logger::INFO,
            true
        ]);

        return true;
    }

    private function createModuleField(&$modelField, $modelFieldScheme, $modelClass, $dataSourceKey)
    {
        $modelField = $modelFieldScheme;

        $this->getLogger()->info([
            '{$0}: Field {$1} in model {$2} successfully created',
            [$dataSourceKey, $modelFieldScheme['fieldName'], $modelClass],
            Logger::INFO,
            true
        ]);
    }

    private function updateModelField($tableField, &$modelField, $fieldName, $modelClass, $dataSourceKey)
    {
        $tableFieldJson = Json::encode($tableField);

        if (crc32($tableFieldJson) == crc32(Json::encode($modelField))) {
            return false;
        }

        $diffField = Json::encode(array_diff($tableField, $modelField));

        $modelField = $tableField;

        $this->getLogger()->info([
            '{$0}: Field {$1} in model {$2} successfully updated: {$3}',
            [$dataSourceKey, $fieldName, $modelClass, $diffField],
            Logger::INFO,
            true
        ]);

        return true;
    }
}