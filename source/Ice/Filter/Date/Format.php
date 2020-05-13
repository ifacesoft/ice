<?php

namespace Ice\Filter;

use DateTime;
use Ice\Core\Filter;
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
        $value = array_key_exists($name, $data) ? $data[$name] : null;

        $date = Date::get($value, Date::FORMAT_MYSQL_DATE, null); // Костыль

        if (!$value || $date === '1970-01-01') {
            return null;
        }

        $dateFormat = empty($filterOptions) ? Date::FORMAT_MYSQL : reset($filterOptions);

        if ($dateFormat === true) {
            $dateFormat = Date::getFormat();
        }

        return Date::get($value, $dateFormat, null);
    }
}