<?php

namespace Ice\Filter;

use Ice\Core\Filter;
use Ice\Exception\Filter_Invalid;

class Type extends Filter
{
    const BOOL = 'boolean';
    const INT = 'integer';
    const FLOAT = 'float';
    const STR = 'string';

    const ARR = 'array';
    const OBJ = 'object';

    /**
     * Filter data
     *
     * @param array $data
     * @param $name
     * @param  mixed $filterOptions
     * @return mixed
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

        settype($value, reset($filterOptions));

        return $value;
    }
}