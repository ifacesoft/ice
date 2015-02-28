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
use Ice\Helper\Arrays;
use Ice\Helper\Date;
use Ice\Helper\File;
use Ice\Helper\Json;
use Ice\Helper\Model as Helper_Model;
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

    private $_tables = null;

    /**
     * Private constructor of dat scheme
     *
     * @param $dataSourceKey
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($dataSourceKey)
    {
        $this->_dataSourceKey = $dataSourceKey;
    }

    /**
     * Create new instance of data scheme
     *
     * @param $dataSourceKey
     * @return Data_Scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function create($dataSourceKey)
    {
        return new Data_Scheme($dataSourceKey);
    }
//
//    /**
//     * Return default scheme name
//     *
//     * @return string
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    protected static function getDefaultKey()
//    {
//        $schemes = array_keys(Data_Source::getConfig()->gets());
//        return reset($schemes);
//    }
//
//    /**
//     * Return map tables short scheme
//     *
//     * @return array
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function getTableNames()
//    {
//        return $this->_dataScheme['tables'];
//    }
//
//    /**
//     * Return map of model classes and their table names
//     *
//     * @return Model[]
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function getModelClasses()
//    {
//        if ($this->_modelClasses !== null) {
//            return $this->_modelClasses;
//        }
//
//        return $this->_modelClasses = array_flip(Arrays::column($this->getTableNames(), 'modelClass'));
//    }
//
//    /**
//     * Return current scheme name
//     *
//     * @return string
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function getName()
//    {
//        return $this->_dataScheme['scheme'];
//    }

    /**
     * Return tables
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getTables(Module $module)
    {
        if ($this->_tables !== null) {
            return $this->_tables;
        }

        $sourceDir = MODULE_DIR . 'Source/';

        $Directory = new RecursiveDirectoryIterator($sourceDir . $module->getAlias() . '/Model');
        $Iterator = new RecursiveIteratorIterator($Directory);
        $Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($Regex as $filePathes) {
            $modelPath = reset($filePathes);
            $classNames = Php::file_get_php_classes($modelPath);
            $modelName = reset($classNames);

            $modelClass = str_replace('/', '\\', substr($modelPath, strlen($sourceDir), -4 - strlen($modelName))) . $modelName;

            $config = $modelClass::getConfig()->gets();
            $config['modelPath'] = substr($modelPath, strlen($sourceDir));
            $this->_tables[$config['scheme']['tableName']] = $config;
        }

        return $this->_tables;
    }

    /**
     * Return data scheme (source) key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getDataSourceKey()
    {
        return $this->_dataSourceKey;
    }
//
//    /**
//     * Return data scheme revision
//     *
//     * @return string
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since 0.5
//     */
//    public function getRevision()
//    {
//        return $this->_dataScheme['revision'];
//    }
//
//    /**
//     * Return data scheme
//     *
//     * @return array
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since 0.5
//     */
//    public function getDataScheme()
//    {
//        return $this->_dataScheme;
//    }
//
//    /**
//     * Return model scheme by table name
//     *
//     * @param $tableName
//     * @return Model_Scheme
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since 0.5
//     */
//    public function getModelScheme($tableName)
//    {
//        return Model_Scheme::create($this->getModelClass($tableName));
//    }
//
//    /**
//     * Return model class by table name
//     *
//     * @param $tableName
//     * @return Model
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since 0.5
//     */
//    public function getModelClass($tableName)
//    {
//        return isset($this->getTables()[$tableName])
//            ? $this->getTables()[$tableName]['modelClass']
//            : Helper_Model::getModelClassByTableName($tableName);
//    }

    /**
     * Return data source
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getDataSource()
    {
        return Data_Source::getInstance($this->getDataSourceKey());
    }

    public function update($force = false)
    {
        $dataSource = $this->getDataSource();

        $module = Module::getInstance();

        $dataSchemeTables = $this->getTables($module);

        foreach ($dataSource->getTables($module) as $tableName => $table) {
            if (!isset($dataSchemeTables[$tableName])) {
                Model::getCodeGenerator()->generate($table, $force);
                Data_Scheme::getLogger()->info(['Create new scheme of table {$0}', $tableName]);
                continue;
            }

            $temp = $table;
            $updated = false;

            $tableSchemeHash = &$dataSchemeTables[$tableName]['schemeHash'];
            $tableScheme = &$dataSchemeTables[$tableName]['scheme'];

            if ($table['schemeHash'] != $tableSchemeHash) {
                Data_Scheme::getLogger()->info(['Updated scheme of table {$0}: {$1}', [$tableName, Json::encode(array_diff($table['scheme'], $tableScheme))]]);
                $tableScheme = $table['scheme'];
                $tableSchemeHash = $table['schemeHash'];
                $updated = true;
            }

            $tableIndexesHash = &$dataSchemeTables[$tableName]['indexesHash'];
            $tableIndexes = &$dataSchemeTables[$tableName]['indexes'];

            if ($table['indexesHash'] != $tableIndexesHash) {
                Data_Scheme::getLogger()->info(['Updated indexes of table {$0}: {$1}', [$tableName, Json::encode(array_diff($table['indexes'], $tableIndexes))]]);
                $tableIndexes = $table['indexes'];
                $tableIndexesHash = $table['indexesHash'];

                $updated = true;
            }

            foreach ($table['columns'] as $columnName => $column) {
                $columnSchemeHash = &$dataSchemeTables[$tableName]['columns'][$columnName]['schemeHash'];
                $columnScheme = &$dataSchemeTables[$tableName]['columns'][$columnName]['scheme'];

                if ($column['schemeHash'] != $columnSchemeHash) {
                    Data_Scheme::getLogger()->info(['Updated column scheme {$0} of table {$1}: {$2}', [$tableName, $columnName, Json::encode(array_diff($column['scheme'], $columnScheme))]]);
                    $columnScheme = $column['scheme'];
                    $columnSchemeHash = $column['schemeHash'];
                    $updated = true;
                }
            }

            if ($updated) {
                Model::getCodeGenerator()->generate($table, $force);
            }

            unset($dataSchemeTables[$tableName]);

        }

        foreach ($dataSchemeTables as $tableName => $table) {
            Data_Scheme::getLogger()->info(['Drop scheme of table {$0}', $tableName]);
            unlink(MODULE_DIR . 'Source/' . $table['modelPath']);
        }

        die();
        $schemeData = [
            'time' => Date::get(),
            'revision' => date('mdHi')
        ];

        $diffTables = Arrays::diff($dataSchemeTables, $dataSourceTables);

        $tables = [
            'updated' => [],
            'notChanged' => []
        ];

        foreach ($diffTables['added'] as $tableName => $table) {
            $table['modelClass'] = Helper_Model::getModelClassByTableName($tableName);

            $modelScheme = Model_Scheme::create($table['modelClass'])
                ->update($dataSource->getDataSourceKey(), $tableName, $force);

            $table['revision'] = $modelScheme->getRevision();
            $tables['updated'][$tableName] = $table;
        }

        foreach ($diffTables['other'] as $tableName => $table) {
            $table['modelClass'] = Helper_Model::getModelClassByTableName($tableName);

            $modelScheme = Model_Scheme::create($table['modelClass']);

            $diffColumns = Arrays::diff($modelScheme->getColumnMapping(), $dataSource->getColumns($tableName));

            if (empty($diffColumns['added']) && empty($diffColumns['deleted'])) {
                $tables['notChanged'][$tableName] = $dataSchemeTables[$tableName];
                continue;
            }

            $table['revision'] = $modelScheme->getRevision();
            $tables['updated'][$tableName] = $table;
        }

        if (empty($diffTables['deleted']) && empty($tables['updated']) && !$force) {
            return $schemeData;
        }

        $schemeData['tables'] = array_merge($tables['updated'], $tables['notChanged']);
        unset($tables);

        ksort($schemeData['tables']);


        $dataSchemeFile = Loader::getFilePath($this->getDataSourceKey(), '.php', 'Var/Scheme/', false, true);

        $prevDataSchemeFile = Loader::getFilePath($this->getDataSourceKey() . '/' . $this->getRevision(), '.php', 'Var/Scheme/', false, true);

        File::move($dataSchemeFile, $prevDataSchemeFile);

        Data_Scheme::getLogger()->info(['Update scheme for tables: {$0}', Php::varToPhpString(array_keys($schemeData['tables']))], Logger::SUCCESS, true);

        return File::createData($dataSchemeFile, $schemeData);
    }
//
//    public function getTableName($modelClass)
//    {
//        foreach ($this->getTables() as $tableName => $table) {
//            if ($table['modelClass'] == $modelClass) {
//                return $tableName;
//            }
//        }
//
//        Data_Scheme::getLogger()->exception(
//            [
//                'Table name not found for class {$0} in data scheme {$1}',
//                [$modelClass, $this->getDataSourceKey()]
//            ],
//            __FILE__, __LINE__, null, null, -1, 'Ice:Data_Scheme_Error'
//        );
//
//        return null;
//    }
}