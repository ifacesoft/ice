<?php

namespace Ice\Filter;

use Ice\Core\Debuger;
use Ice\Core\Filter;
use Ice\Exception\Filter_Invalid;

class DefaultValue extends Filter
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
        if (empty($filterOptions)) {
            throw new Filter_Invalid(['Filter options of filter {$0} is empty', get_class($this)]);
        }

        $value = array_key_exists($name, $data) ? $data[$name] : null;

        $default = isset($filterOptions['default'])
            ? (array) $filterOptions['default']
            : [null, ''];

        $isEmpty = false;

        foreach ($default as $def) {
            if ($value === $def) {
                $isEmpty = true;
                break;
            }
        }

        if ($isEmpty) {
            $value = isset($filterOptions['value'])
                ? $filterOptions['value']
                : reset($filterOptions);
        }

        return $value;
    }
}