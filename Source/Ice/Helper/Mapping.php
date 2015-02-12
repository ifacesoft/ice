<?php

namespace Ice\Helper;

use Ice\Core\Model as Core_Model;

class Mapping
{
    /**
     * @param Core_Model $modelClass
     * @param $fieldNames
     * @return array
     */
    public static function columnNames($modelClass, $fieldNames)
    {
        $modelMapping = $modelClass::getScheme()->getFieldNames();

        return array_map(
            function ($fieldName) use ($modelMapping) {
                return $modelMapping[$fieldName];
            },
            $fieldNames
        );
    }
}