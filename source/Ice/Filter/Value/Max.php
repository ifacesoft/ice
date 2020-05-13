<?php

namespace Ice\Filter;

use Ice\Core\Filter;
use Ice\Exception\Filter_Invalid;

class Value_Max extends Filter
{
    /**
     * Filter data
     *
     * ```php
     *
     *  $filter = [
     *      'valueName' => ['filters' => [DefaultValue::class => 'defaultValue']]
     * ];
     *
     *  $filter = [
     *      'valueName' => ['filters' => [DefaultValue::class => ['value' => 'defaultValue', 'default' => 0]]
     * ];
     *
     * @param array $data
     * @param $name
     * @param  mixed $filterOptions
     * @return bool
     * @author anonymous <email>
     *
     * @version 1.4
     * @since   1.4
     */
    public function filter(array $data, $name, array $filterOptions)
    {
        $value = array_key_exists($name, $data) ? $data[$name] : null;

        $max = reset($filterOptions);

        if ($data[$name] > $max) {
            return $max;
        }

        return $value;
    }
}