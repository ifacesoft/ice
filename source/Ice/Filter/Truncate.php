<?php

namespace Ice\Filter;

use Ice\Core\Filter;
use Ice\Exception\Filter_Invalid;
use Ice\Helper\Date;
use Ice\Helper\Type_String;

class Truncate extends Filter
{

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
        $value = empty($data[$name]) ? null : $data[$name];

        if (!$value) {
            return null;
        }

        $truncate = empty($filterOptions) ? '255' : reset($filterOptions);

        return Type_String::truncate($value, $truncate);
    }
}