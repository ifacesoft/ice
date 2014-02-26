<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.02.14
 * Time: 20:51
 */

namespace ice\action;

use ice\core\Action;
use ice\core\action\Cliable;
use ice\core\Action_Context;
use ice\core\Config;
use ice\core\Data_Mapping;
use ice\core\Data_Source;
use ice\core\helper\Model;

class Data_Mapping_Sync extends Action implements Cliable
{
    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        $dataSchemeTableNames = array_keys(Data_Source::getDefault()->getDataScheme());
        $dataMappingConfigData = array(
            Data_Mapping::CONFIG_PREFIXES => Data_Mapping::get()->getPrefixes(),
            Data_Mapping::CONFIG_TABLES => Data_Mapping::get()->getModelClasses()
        );

        foreach ($dataSchemeTableNames as $tableName) {
            $prefix = strstr($tableName, '_', true);

            if (!$prefix) {
                continue;
            }

            $prefix[0] = strtoupper($prefix[0]);

            if (!array_key_exists($prefix, $dataMappingConfigData[Data_Mapping::CONFIG_PREFIXES])) {
                $dataMappingConfigData[Data_Mapping::CONFIG_PREFIXES][$prefix] = '\\' . $prefix;
            }
        }

        foreach (array_diff($dataSchemeTableNames, array_values($dataMappingConfigData[Data_Mapping::CONFIG_TABLES])) as $tableName) {
            $prefix = strstr($tableName, '_', true);

            $modelName = Model::tableToModel($tableName);

            if ($prefix) {
                $prefix[0] = strtoupper($prefix[0]);
                $modelName = $prefix . ':' . $modelName;
            }

            $dataMappingConfigData[Data_Mapping::CONFIG_TABLES][$modelName] = $tableName; // TODO: implements gets model class
        }

        Config::create(Data_Mapping::getClass(), $dataMappingConfigData);
    }
}