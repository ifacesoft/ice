<?php

namespace Ice\Core;

use Ice\Core;

trait Stored
{
    use Core;

    /**
     * Restore object
     *
     * @param  array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function __set_state(array $data)
    {
        $class = self::getClass();

        $object = new $class();

        foreach ($data as $fieldName => $fieldValue) {
            $object->$fieldName = $fieldValue;
        }

        return $object;
    }
}
