<?php

namespace Ice\Filter;

use Ice\Core\Debuger;
use Ice\Core\Filter;
use Ice\Core\Module;
use Ice\Exception\Filter_Invalid;
use Ice\Helper\Date;

class Date_Format extends Filter
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
        if (empty($filterOptions)) {
            throw new Filter_Invalid(['Filter options of filter {$0} is empty', get_class($this)]);
        }

        $value = empty($data[$name]) ? null : $data[$name];

        if (!$value) {
            return null;
        }

        $dateFormat = reset($filterOptions);

        if ($dateFormat === true) {
            $dateFormat = Date::getFormat();
        }

        return Date::get($value, $dateFormat, null);
    }
}