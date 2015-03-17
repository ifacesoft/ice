<?php
/**
 * Ice core data scheme container class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Helper\Directory;
use Ice\Helper\Json;
use Ice\Helper\Php;
use Ice\Helper\String;
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
 * @package Ice
 * @subpackage Core
 */
class Data_Scheme
{
    use Core;

    /**
     * Data source key
     *
     * @var string
     */
    private $_dataSourceKey = null;

    private static $_tables = null;

    /**
     * Private constructor of dat scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     * @param $dataSourceKey
     */
    private function __construct($dataSourceKey)
    {
        $this->_dataSourceKey = $dataSourceKey;
    }

    /**
     * Create new instance of data scheme
     * @param $dataSourceKey
     * @return Data_Scheme
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function create($dataSourceKey)
    {
        return new Data_Scheme($dataSourceKey);
    }

    /**
     * Return tables
     *
     * @param Module $module
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getTables(Module $module)
    {
        if (self::$_tables !== null) {
            return self::$_tables;
        }

        self::$_tables = [];

        $sourceDir = $module->get(Module::SOURCE_DIR);

        $Directory = new RecursiveDirectoryIterator(Directory::get($sourceDir . $module->getAlias() . '/Model'));
        $Iterator = new RecursiveIteratorIterator($Directory);
        $Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($Regex as $filePathes) {
            $modelPath = reset($filePathes);
            $classNames = Php::getClassNamesFromFile($modelPath);
            $modelName = reset($classNames);

            $modelClass = str_replace('/', '\\', substr($modelPath, strlen($sourceDir), -4 - strlen($modelName))) . $modelName;

            $config = $modelClass::getConfig()->gets();

            $config['modelClass'] = $modelClass;
            $config['modelPath'] = substr($modelPath, strlen($sourceDir));
            self::$_tables[$config['dataSourceKey']][$config['scheme']['tableName']] = $config;
        }

        return self::$_tables;
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
                    Data_Scheme::getLogger()->info(['Update scheme for model {$0}: {$1}', [$schemeTables[$tableName]['modelClass'], Json::encode(array_diff($table['scheme'], $tableScheme))]]);
                    $tableScheme = $table['scheme'];
                    $tableSchemeHash = $table['schemeHash'];
                    $updated = true;
                }

                $tableIndexesHash = &$schemeTables[$tableName]['indexesHash'];
                $tableIndexes = &$schemeTables[$tableName]['indexes'];

                if ($table['indexesHash'] != $tableIndexesHash) {
                    Data_Scheme::getLogger()->info(['Update indexes for model {$0}: {$1}', [$schemeTables[$tableName]['modelClass'], Json::encode($table['indexes'])]]);
                    $tableIndexes = $table['indexes'];
                    $tableIndexesHash = $table['indexesHash'];
                    $updated = true;
                }

                $dataSchemeColumns = $schemeTables[$tableName]['columns'];

                foreach ($table['columns'] as $columnName => $column) {
                    if (!isset($schemeTables[$tableName]['columns'][$columnName])) {
                        $schemeTables[$tableName]['columns'][$columnName] = [
                            'scheme' => $column['scheme'],
                            'schemeHash' => $column['schemeHash']
                        ];
                        Data_Scheme::getLogger()->info(['Create field {$0} for model {$1}', [$column['fieldName'], $schemeTables[$tableName]['modelClass']]]);
                        $updated = true;
                        continue;
                    }

                    $columnSchemeHash = &$schemeTables[$tableName]['columns'][$columnName]['schemeHash'];
                    $columnScheme = &$schemeTables[$tableName]['columns'][$columnName]['scheme'];

                    if ($column['schemeHash'] != $columnSchemeHash) {
                        Data_Scheme::getLogger()->info(['Update field {$0} for model {$1}: {$2}', [$column['fieldName'], $schemeTables[$tableName]['modelClass'], Json::encode(array_diff($column['scheme'], $columnScheme))]]);
                        $columnScheme = $column['scheme'];
                        $columnSchemeHash = $column['schemeHash'];
                        $updated = true;
                    }

                    unset($dataSchemeColumns[$columnName]);
                }

                foreach ($dataSchemeColumns as $columnName => $column) {
                    Data_Scheme::getLogger()->info(['Remove field {$0} for model {$1}', [$column['fieldName'], $schemeTables[$tableName]['modelClass']]]);
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
                    unlink($module->get(Module::SOURCE_DIR) . $table['modelPath']);
                }
            }
        }
    }
}