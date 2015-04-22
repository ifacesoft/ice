<?php
/**
 * Ice core data scheme container class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Directory;
use Ice\Helper\Json;
use Ice\Helper\Php;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class Data_Scheme
 *
 * Core data scheme container class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Data_Scheme
{
    use Core;

    private static $tables = null;
    /**
     * Data source key
     *
     * @var string
     */
    private $dataSourceKey = null;

    /**
     * Private constructor of dat scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     * @param   $dataSourceKey
     */
    private function __construct($dataSourceKey)
    {
        $this->dataSourceKey = $dataSourceKey;
    }

    /**
     * Create new instance of data scheme
     *
     * @param  $dataSourceKey
     * @return Data_Scheme
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public static function create($dataSourceKey)
    {
        return new Data_Scheme($dataSourceKey);
    }

    public static function update(Module $module, $force = false)
    {
        $dataSchemeTables = Data_Scheme::getTables($module);

        foreach ($module->getDataSourceTables() as $dataSourceKey => $tables) {
            Data_Scheme::getLogger()->info(['Checking models from data source {$0}', $dataSourceKey]);

            $schemeTables = &$dataSchemeTables[$dataSourceKey];

            foreach ($tables as $tableName => $table) {
                if (!isset($schemeTables[$tableName])) {
                    $modelClass = $module->getModelClass($tableName, $dataSourceKey);
                    Model::getCodeGenerator()->generate($modelClass, $table, $force);
                    Data_Scheme::getLogger()->info(['Create model {$0}', $modelClass]);
                    continue;
                }

                $temp = $table;
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
                if ($force || $table['oneToManyHash'] != $schemeTables[$tableName]['oneToManyHash']) {
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
                if ($force || $table['manyToOneHash'] != $schemeTables[$tableName]['manyToOneHash']) {
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
                if ($force || $table['manyToManyHash'] != $schemeTables[$tableName]['manyToManyHash']) {
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
                    Model::getCodeGenerator()->generate($schemeTables[$tableName]['modelClass'], $table, $force);
                }

                unset($schemeTables[$tableName]);
            }
        }

        foreach ($dataSchemeTables as $dataSourceKey => $schemeTables) {
            if (!empty($schemeTables)) {
                Data_Scheme::getLogger()->info(['Removing models from data source {$0}', $dataSourceKey]);
                foreach ($schemeTables as $tableName => $table) {
                    Data_Scheme::getLogger()->info(['Remove model {$0}', $schemeTables[$tableName]['modelClass']]);
                    $modelFilePath = $module->get(Module::SOURCE_DIR) . $table['modelPath'];
                    /** @bug @todo Not need delete file if he included in other data source scheme */
                    if (file_exists($modelFilePath)) {
                        unlink($modelFilePath);
                    }
                }
            }
        }
    }

    /**
     * Return tables
     *
     * @param  Module $module
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getTables(Module $module)
    {
        if (self::$tables !== null) {
            return self::$tables;
        }

        self::$tables = [];

        $sourceDir = $module->get(Module::SOURCE_DIR);

        $Directory = new RecursiveDirectoryIterator(Directory::get($sourceDir . $module->getAlias() . '/Model'));
        $Iterator = new RecursiveIteratorIterator($Directory);
        $Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($Regex as $filePathes) {
            $modelPath = reset($filePathes);
            $classNames = Php::getClassNamesFromFile($modelPath);
            $modelName = reset($classNames);

            $modelClass = str_replace(
                    '/',
                    '\\',
                    substr($modelPath, strlen($sourceDir), -4 - strlen($modelName))
                ) . $modelName;

            $config = $modelClass::getConfig()->gets();

            $config['modelClass'] = $modelClass;
            $config['modelPath'] = substr($modelPath, strlen($sourceDir));
            self::$tables[$config['dataSourceKey']][$config['scheme']['tableName']] = $config;
        }

        return self::$tables;
    }
}
