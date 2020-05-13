<?php

namespace Ice\Filter;

use Ice\Core\Filter;
use Ice\Exception\Filter_Invalid;

class Trim extends Filter
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
     * @throws Filter_Invalid
     * @author anonymous <email>
     *
     * @version 1.4
     * @since   1.4
     */
    public function filter(array $data, $name, array $filterOptions)
    {
        $value = array_key_exists($name, $data) ? $data[$name] : null;

        if ($value !== null && is_string($value)) {
            $value = trim($value);
        }

        return $value ;
    }
}